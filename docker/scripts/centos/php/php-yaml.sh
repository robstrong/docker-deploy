#!/bin/bash
set -e

wget http://pyyaml.org/download/libyaml/yaml-0.1.4.tar.gz -O /root/yaml.tar.gz
tar xzvf /root/yaml.tar.gz -C /root/
cd /root/yaml-0.1.4 
./configure --prefix=/usr/local 
/usr/bin/make 
/usr/bin/make install
pecl install yaml
echo "extension=yaml.so" >> /etc/php.ini
rm -Rf /root/yaml-0.1.4 yaml.tar.gz
