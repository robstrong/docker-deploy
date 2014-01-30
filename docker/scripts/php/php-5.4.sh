#!/bin/bash
set -e

yum -y --enablerepo=remi install php php-common php-cli php-pear php-pdo php-pgsql php-pecl-memcache php-pecl-memcached php-gd php-mbstring php-mcrypt php-xml php-devel php-process
CURR_DIR="`dirname \"$0\"`"
cp $CURR_DIR/php.ini /etc/php.ini
