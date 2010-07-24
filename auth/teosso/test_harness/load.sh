#!/bin/sh
 sudo cp -r lib/MyApache   /usr/local/lib/site_perl/
 sudo  /etc/init.d/apache2 stop
 sleep 3
 sudo  /etc/init.d/apache2 start
