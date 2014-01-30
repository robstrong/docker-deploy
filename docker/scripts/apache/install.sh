#!/bin/bash
set -e

CACHE_PATH="`dirname \"$0\"`/../../cache"
echo $CACHE_PATH
if [ ! -f "$CACHE_PATH/httpd-2.4.7.tar.gz" ]; then
    wget http://apache.tradebit.com/pub/httpd/httpd-2.4.7.tar.gz -O $CACHE_PATH/httpd-2.4.7.tar.gz
fi
cp $CACHE_PATH/httpd-2.4.7.tar.gz .
gzip -d httpd-2.4.7.tar.gz
tar xfv httpd-2.4.7.tar

./httpd-2.4.7/configure --enable-so
./httpd-2.4.7/make
./httpd-2.4.7/make install
