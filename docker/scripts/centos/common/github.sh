#!/bin/bash
set -e

SSH_DIR=/root/.ssh
if [ ! -d "$SSH_DIR" ]; then
    mkdir -p $SSH_DIR
fi

echo -e "Host github.com\n\tStrictHostKeyChecking no\n" >> /root/.ssh/config
echo -e "Host *\n\tLogLevel error\n" >> /root/.ssh/config
