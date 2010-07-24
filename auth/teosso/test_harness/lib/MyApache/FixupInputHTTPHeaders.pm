package MyApache::FixupInputHTTPHeaders;

use strict;
use warnings FATAL => 'all';

use mod_perl2;

use base qw(Apache2::Filter);

use Apache2::RequestUtil ();
use Apache2::Connection ();
use APR::Brigade ();
use APR::Bucket ();

use Apache::TestTrace;

use Data::Dumper;

use constant DEBUG => 1;

use subs qw(mydebug);
#*mydebug = DEBUG ? \&Apache::TestTrace::debug : sub {};
*mydebug = DEBUG ? sub { warn scalar localtime(), ": ", @_, "\n"; } : sub {};

use Apache2::Const -compile => qw(OK DECLINED CONN_KEEPALIVE);
use APR::Const    -compile => ':common';


sub manip { 
     my ($class, $ra_headers, $f) = @_;
     my ($method, $uri) = $ra_headers->[0] =~ /(\w+)\s+([^\s]+)/;
     #return unless $uri =~ /login\/index\.php/;
     return unless $uri =~ /auth\/teosso\//;
     mydebug "headers: " .Dumper($ra_headers);

     # Simulated auth header from /login
     return unless grep(/MyXAuth=XSimLoggedIn/, @{$ra_headers});

     mydebug "METHOD: $method URI: $uri";
     #my $header = "SM_USER: <cpm><acct_id>123456</acct_id><acct_name>hoang</acct_name><email>hoang.m.nguyen\@intel.com</email><firstn>Hoang</firstn><lastn>Nguyen</lastn></cpm>\n";
     #my $header = "SM_USER: <cpm><acct_id>789012</acct_id><acct_name>piers</acct_name><email>piers\@catalyst.net.nz</email><firstn>Piers</firstn><lastn>Harding</lastn></cpm>\n";
     #my $header = "SM_USER: <cpm><acct_id>654321</acct_id><acct_name>john</acct_name><email>john\@catalyst.net.nz</email><firstn>John</firstn><lastn>Doe</lastn></cpm>\n";
     #my $header = "SM_USER: <cpm><acct_id>999991</acct_id><acct_name>johnny</acct_name><email>jonny\@intel.com</email><firstn>John</firstn><lastn>Learner</lastn></cpm>\n";
     #my $header = "SM_USER: <cpm><acct_id>999991</acct_id><lastn>Doe</lastn></cpm>\n";
     #my $header = "SM_USER: <cpm><acct_id>888883</acct_id><acct_name>daisy</acct_name><email>daisy\@intel.com</email><firstn>Daisy</firstn><lastn>Duck</lastn></cpm>\n";
#     my @headers = ("HTTP_EBUSAGENTID: 888883\n", "HTTP_LOGINID: daisy\n", "HTTP_EMAIL: daisy\@intel.com\n", "HTTP_FIRSTNAME: Daisy\n", "HTTP_LASTNAME: Duck\n");
#     my @headers = ("HTTP_EBUSAGENTID: 888882\n");
#     my @headers = ("HTTP_EBUSAGENTID: 999991\n", "HTTP_LOGINID: johnny\n", "HTTP_EMAIL: jonny\@intel.com\n", "HTTP_FIRSTNAME: Johnny\n", "HTTP_LASTNAME: Learner\n");
#     my @headers = ("HTTP_EBUSAGENTID: 999991\n");
#     my @headers = ("HTTP_EBUSAGENTID: 789012\n", "HTTP_LOGINID: piers\n", "HTTP_EMAIL: piers\@catalyst.net.nz\n", "HTTP_FIRSTNAME: Piers\n", "HTTP_LASTNAME: Harding\n");
#     my @headers = ("HTTP_EBUSAGENTID: 888884\n", "HTTP_LOGINID: daffy\n", "HTTP_EMAIL: daffy\@intel.com\n", "HTTP_FIRSTNAME: Daffy\n", "HTTP_LASTNAME: Duck\n");
     my @headers = ("HTTP_EBUSAGENTID: 888884\n", "HTTP_LOGINID: donald\n", "HTTP_FIRSTNAME: Daffy-Donald\n");
     mydebug "Adding header: @headers";
     push(@$ra_headers, @headers);
}


# perl < 5.8 can't handle more than one attribute in the subroutine
# definition so add the "method" attribute separately
use attributes ();
attributes::->import(__PACKAGE__ => \&handler, "method");

sub handler : FilterConnectionHandler {

    # $mode, $block, $readbytes are passed only for input filters
    # so there are 3 more arguments
    return @_ == 6 ? handle_input(@_) : handle_output(@_);

}

sub context {
    my ($f) = shift;

    my $ctx = $f->ctx;
    unless ($ctx) {
        #mydebug "filter context init";
        $ctx = {
            headers             => [],
            done_with_headers   => 0,
            seen_body_separator => 0,
            keepalives          => $f->c->keepalives,
        };
        # since we are going to manipulate the reference stored in
        # ctx, it's enough to store it only once, we will get the same
        # reference in the following invocations of that filter
        $f->ctx($ctx);
        return $ctx;
    }

    my $c = $f->c;
    if ($c->keepalive == Apache2::Const::CONN_KEEPALIVE &&
        $ctx->{done_with_headers} &&
        $c->keepalives > $ctx->{keepalives}) {

        # mydebug "a new request resetting the input filter state";

        $ctx->{headers}             = [];
        $ctx->{done_with_headers}   = 0;
        $ctx->{seen_body_separator} = 0;
        $ctx->{keepalives} = $c->keepalives;
    }

    return $ctx;
}

sub handle_output {
    my($class, $f, $bb) = @_;

    my $ctx = context($f);

    # handling the HTTP request body
    if ($ctx->{done_with_headers}) {
        mydebug "passing the body through unmodified";
        my $rv = $f->next->pass_brigade($bb);
        return $rv unless $rv == APR::Const::SUCCESS;
        return Apache2::Const::OK;
    }

    $bb->flatten(my $data);

    mydebug "data: $data\n";

    my $c = $f->c;
    my $ba = $c->bucket_alloc;
    while ($data =~ /(.*\n)/g) {
        my $line = $1;
        mydebug "READ: [$line]";
        if ($line =~ /^[\r\n]+$/) {
            # let the user function do the manipulation of the headers
            # without the separator, which will be added when the
            # manipulation has been completed
            $ctx->{done_with_headers}++;
            $class->manip($ctx->{headers}, $f);
            my $data = join '', @{ $ctx->{headers} }, "\n";
            $ctx->{headers} = [];

            my $out_bb = APR::Brigade->new($c->pool, $ba);
            $out_bb->insert_tail(APR::Bucket->new($ba, $data));

            my $rv = $f->next->pass_brigade($out_bb);
            return $rv unless $rv == APR::Const::SUCCESS;

            return Apache2::Const::OK;
            # XXX: is it possible that some data will be along with
            # headers in the same incoming bb?
        }
        else {
            push @{ $ctx->{headers} }, $line;
        }
    }

    return Apache2::Const::OK;
}

sub handle_input {
    my($class, $f, $bb, $mode, $block, $readbytes) = @_;

    my $ctx = context($f);

    # handling the HTTP request body
    if ($ctx->{done_with_headers}) {
        mydebug "passing the body through unmodified";
        return Apache2::Const::DECLINED;
    }

    # any custom input HTTP header buckets to inject?
    return Apache2::Const::OK if inject_header_bucket($bb, $ctx);

    # normal HTTP headers processing
    my $c = $f->c;
    until ($ctx->{seen_body_separator}) {
        my $ctx_bb = APR::Brigade->new($c->pool, $c->bucket_alloc);
        my $rv = $f->next->get_brigade($ctx_bb, $mode, $block, $readbytes);
        return $rv unless $rv == APR::Const::SUCCESS;

        while (!$ctx_bb->is_empty) {
            my $b = $ctx_bb->first;

            if ($b->is_eos) {
                mydebug "EOS!!!";
                $b->remove;
                $bb->insert_tail($b);
                last;
            }

            my $len = $b->read(my $data);

            # leave the non-data buckets as is
            unless ($len) {
                $b->remove;
                $bb->insert_tail($b);
                next;
            }

            # XXX: losing meta buckets here
            $b->delete;
            #mydebug "filter read:\n[$data]";

            if ($data =~ /^[\r\n]+$/) {
                # normally the body will start coming in the next call to
                # get_brigade, so if your filter only wants to work with
                # the headers, it can decline all other invocations if that
                # flag is set. However since in this test we need to send 
                # a few extra bucket brigades, we will turn another flag
                # 'done_with_headers' when 'seen_body_separator' is on and
                # all headers were sent out
                # mydebug "END of original HTTP Headers";
                $ctx->{seen_body_separator}++;

                # let the user function do the manipulation of the headers
                # without the separator, which will be added when the
                # manipulation has been completed
                $class->manip($ctx->{headers}, $f);

                # but at the same time we must ensure that the
                # the separator header will be sent as a last header
                # so we send one newly added header and push the separator
                # to the end of the queue
                push @{ $ctx->{headers} }, "\n";
                # mydebug "queued header [$data]";
                inject_header_bucket($bb, $ctx);
                last; # there should be no more headers in $ctx_bb
                # notice that if we didn't inject any headers, this will
                # still work ok, as inject_header_bucket will send the
                # separator header which we just pushed to its queue
            } else {
                push @{ $ctx->{headers} }, $data;
            }
        }
    }

    return Apache2::Const::OK;
}

# returns 1 if a bucket with a header was inserted to the $bb's tail,
# otherwise returns 0 (i.e. if there are no headers to insert)
sub inject_header_bucket {
    my ($bb, $ctx) = @_;

    return 0 unless @{ $ctx->{headers} };

    # extra debug, wasting cycles
    my $data = shift @{ $ctx->{headers} };
    $bb->insert_tail(APR::Bucket->new($bb->bucket_alloc, $data));
    #mydebug "injected header: [$data]";

    # next filter invocations will bring the request body if any
    if ($ctx->{seen_body_separator} && !@{ $ctx->{headers} }) {
        $ctx->{done_with_headers}   = 1;
    }

    return 1;
}

1;
__END__

=pod

=head1 NAME

copied from Apache2::Filter::HTTPHeadersFixup - Manipulate Apache 2 HTTP Headers

=head1 Synopsis

  # httpd.conf
  <VirtualHost Zoot>
      PerlModule MyApache::FixupInputHTTPHeaders
      PerlInputFilterHandler MyApache::FixupInputHTTPHeaders
  </VirtualHost>

  # similar for output headers

=head1 Description

C<Apache2::Filter::HTTPHeadersFixup> injects Authorisation Headers into 
the HTTP Request inorder to test the linkage with Moodle and Intels
CA Federation manager.

It supports KeepAlive connections.

This class cannot be used as is. It has to be sub-classed. Read on.

=head1 Debug

C<Apache2::Filter::HTTPHeadersFixup> includes internal tracing calls,
which make it easy to debug the parsing of the headers.

First change the constant DEBUG to 1 in
C<Apache2::Filter::HTTPHeadersFixup>. Then enable Apache-Test debug
tracing. For example to run a test with tracing enabled do:

  % t/TEST -trace=debug -v manip/out_append

Or you can set the C<APACHE_TEST_TRACE_LEVEL> environment variable to
I<debug> at the server startup:

  APACHE_TEST_TRACE_LEVEL=debug apachectl start

All the tracing goes into I<error_log>.

=head1 Bugs

=head1 See Also

L<Apache2>, L<mod_perl2>, L<Apache2::Filter>




=head1 Author

Piers Harding

Previously developed by
Philip M. Gollucci
Stas Bekman.



=head1 Copyright

The C<MyApache2::HTTPHeadersFixup> module is free software; you
can redistribute it and/or modify it under the same terms as Perl
itself.

=cut



