package MyApache::Login;

use strict;

use mod_perl2;
use Apache2::RequestRec ();
use Apache2::RequestIO ();
use APR::Table;
use CGI::Cookie ();
use Apache2::Const -compile => qw(OK REDIRECT);
use vars qw($VERSION);
$VERSION = '0.01';

use Data::Dumper;

=pod

=head1 B<Login - Complete the login cycle by redirecting back to the RESUMEPATH>

=head2 B<Module Usage>

=over
  This module is used to simulate the login process from CA Federation Manager

   perlModule MyApache::Login
   <Location /login>
      PerlAccessHandler MyApache::Login
   </Location>

=back

=cut

sub handler {

  my $r = shift;
  print STDERR __PACKAGE__ ." uri: ".$r->uri ." method: ".$r->method ."\n";
  if ($r->method eq 'GET') {
    my $action = $r->uri.'?'.$r->args;
    $r->content_type('text/html');
    $r->print(<<EOF);
      <!doctype html public "-//w3c//dtd html 4.0 transitional//en">
      <html>
        <head><title>Test Login Page</title></head>
        <body>
          <h1>Test login </h1>
          <form method='POST' action='$action'>
            <input type='submit' name='go' value='Go!' />
          </form>
        </body>
      </html>
EOF
    print STDERR __PACKAGE__ ." giving login page \n";
    return Apache2::Const::OK;
  }
  else {
    my $args = $r->args();
    my %args;
    map { my ($k,$v) = split(/=/,$_); $args{$k} = $v; } (split(/\&/,$args));
    print STDERR __PACKAGE__ ." args: ".Dumper( \%args );
 
    my $resumepath = URLDecode($args{'target'});
    print STDERR __PACKAGE__ ." target: $resumepath \n";

    $r->content_type('text/html');
    my $cookie = new CGI::Cookie( -name=> 'MyXAuth', -value=> 'XSimLoggedIn', -path=> '/', -expires=> '+3d');
    print STDERR __PACKAGE__ ." setting cookie: ".$cookie." \n";
    $r->err_headers_out->add('Set-Cookie' => $cookie);
    $r->headers_out->set('Location' => $resumepath);
    return Apache2::Const::REDIRECT;
  }
}

sub URLDecode {
  my $s = shift;
  $s =~ s/%([a-fA-F0-9]{2})/chr(hex($1))/eg;
  return $s;
}

1;

