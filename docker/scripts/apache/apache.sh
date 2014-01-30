#!/bin/bash
set -e

yum -y --enablerepo=remi install httpd mod_ssl
mkdir -p /etc/httpd/conf
cp /root/docker/httpd.conf /etc/httpd/conf/httpd.conf
mkdir -p /var/www
useradd www-data
