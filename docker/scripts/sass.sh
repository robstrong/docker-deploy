#!/bin/bash
set -e

yum -y install ruby rubygems
gem install sass --no-ri --no-rdoc
