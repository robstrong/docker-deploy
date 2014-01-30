#!/bin/bash
set -e

yum install -y supervisor
cp /root/docker/supervisord.conf /etc/supervisord.conf
