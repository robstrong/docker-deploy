#!/bin/bash
set -e

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
