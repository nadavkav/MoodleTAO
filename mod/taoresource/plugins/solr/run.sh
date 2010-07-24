#!/bin/sh
# example of steps to reload a SOLR index

ps axf | grep start.jar | grep -v grep | awk '{print $1}'| xargs kill
sleep 3
ps axf | grep start.jar | grep -v grep | awk '{print $1}'| xargs kill
sleep 2
cd ~/workspace/moodleintel/mod/taoresource/plugins/solr
php export.php > ~/code/lucene/apache-solr-1.3.0/imsrepo/exampledocs/tao.xml

cd ~/code/lucene/apache-solr-1.3.0/imsrepo/solr/data && rm -rf *

cd ~/code/lucene/apache-solr-1.3.0/imsrepo 

java -jar start.jar &

sleep 6

cd ~/code/lucene/apache-solr-1.3.0/imsrepo/exampledocs
./post.sh tao.xml
sleep 1

GET 'http://localhost:7574/solr/select/?q=Two&version=2.2&start=0&rows=10&indent=on'
