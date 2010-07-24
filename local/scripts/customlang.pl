#!/usr/bin/perl -w

=doc
Description: 

This script reads your language files and replaces 
specified strings with your local customisations. 

Hot to Use: 

First, update the CONFIG variables at the beginning of the script
to fit your local needs.

Copy the script to your moodle root directory and run it from there. 

e.g.

perl /var/www/moodle/customlang.pl

New language files are created in $customlangdir leaving your original 
files untouched.  

=cut

use strict;

# CONFIG: specify here the location of your language files #
my $customlangdir = "lang/en_xx_utf8";
my $originlangdir = "lang/en_utf8";

# CONFIG: specify here which strings to replace #
my %replacements = (
    course => 'learning path',
);

# create an array of all the files we want to change
my @files = `find $originlangdir -type f`;

# create the customlang dir if it doesn't alreay exist
if (-e $customlangdir && -d $customlangdir) { 
    `rm $customlangdir/*`;
} else { 
    mkdir "$customlangdir"
      or die "Cannot create lang/$customlangdir $!";
}

# copy all the original files into the custom directory
`cp $originlangdir/* $customlangdir/`;

foreach my $f (@files) {
    if ($f =~ m!^$originlangdir/help/!
    || $f =~ m!^$originlangdir/docs?/!
    || $f =~ m!^$originlangdir/README!
    || $f =~ m!^$originlangdir/fonts/! ) {
        next ; # skip help files
    }
    my $fname = $f;
    my $filemodified = 0;
    $fname =~ s!^$originlangdir!!;

    open  (FROMFILE, $f) or die "Cannot open $f $!";

    open  TOFILE, ">$customlangdir/$fname" or die "Cannot open $fname $!";
    print TOFILE "<?php \n";

    my $str = '';
    while (<FROMFILE>) {
        if (m/^\$string\['\w+'\](.*)$/) {
            # matched a new $string line
            # deal with the previousone
            my $newstr = $&;
            do_stringline($str, \$filemodified);
            $str = $newstr;
        } else {
            # may be a continuation line, or just noise
            $str .= $_;
        }
    }
    do_stringline($_, \$filemodified); # work out the remainder

    print TOFILE "\n?>";
    close FROMFILE;

    if (!$filemodified) {
        # delete the file if nothing changed to it
#        warn "Nothing changed deleting $fname";
        `rm $customlangdir/$fname`;
    } else {
        print "\nUpdated file: $fname\n";
    }
}

sub do_stringline {

    my ($str, $filemodified_ref) = @_;
    unless ($str) {
        return 1;
    }
    if ( ($str =~ m/^<\?php/i) || ($str =~ m/\?>$/) ) {
        return 1;
    }

    ## special rules now
    my $needsreplacing = 0;
    foreach my $key (keys %replacements) {
        if ($str =~ m/$key/i) {
            $needsreplacing = 1;
        }
    }
    return unless $needsreplacing;

    $str =~ m/^(.*?)=(.+)$/s;

    my $pre  = $1;
    my $post = $2;

    for my $key (keys %replacements) {
        my $value = $replacements{$key};

        my $uckey   = ucfirst $key;
        my $ucvalue = ucfirst $value;

        # first just update everything we find - we reset exceptions following
        $post =~ s/\b${key}\b/${value}/g;
        $post =~ s/\b${uckey}\b/${ucvalue}/g;
        $post =~ s/\b${key}s\b/${value}s/g;
        $post =~ s/\b${uckey}s\b/${ucvalue}s/g;

        # reset things that look like variables
        my ($first, @rest) = split(//, $value);
        my $rest = join('', @rest);

        $post =~ s/(\$[\w\=\>]*)$first$rest/$1$key/g;
        $post =~ s/(\=|\>)$value/$1$key/g;

        # reset things that look like url's
        $post =~ s/\/$value/\/$key/g;
        $post =~ s/$value\//$key\//g;

    }

    my $newstr  = "$pre=$post";
    if ($newstr eq $str) {
        #warn "Nothing changed in $str\n";
        return 1;
    } else {
        print "Origin lang string: $str\n";
        #print "String pre: $pre\n";
        #print "String post: $post\n";
        print "Custom lang string: $newstr\n\n";
    }

    ## and print it
    print TOFILE $newstr . "\n";
    $$filemodified_ref = 1;
}
