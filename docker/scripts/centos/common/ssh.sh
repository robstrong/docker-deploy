#!/bin/bash
set -e

yum install -y openssh openssh-server openssh-clients
sshRun="/var/run/sshd"
if [ ! -d "$sshRun" ]
then
    mkdir /var/run/sshd
fi

sshPath="/root/.ssh"
if [ ! -d "$sshPath" ]
then
    mkdir /root/.ssh
fi


privateKey="/root/docker/id_dsa"
if [ -f "$privateKey" ]
then
    cp $privateKey /root/.ssh/id_dsa
    chmod 600 /root/.ssh/id_dsa
fi

publicKey="/root/docker/id_dsa.pub"
if [ -f "$publicKey" ]
then
    cat $publicKey >> /root/.ssh/authorized_keys
fi

rsaPrivateKey="/root/docker/id_rsa"
if [ -f "$rsaPrivateKey" ]
then
    cp $rsaPrivateKey /root/.ssh/id_rsa
    chmod 600 /root/.ssh/id_rsa
fi

rsaPublicKey="/root/docker/id_rsa.pub"
if [ -f "$rsaPublicKey" ]
then
    cat $rsaPublicKey >> /root/.ssh/authorized_keys
fi

cp /root/docker/sshd_config /etc/ssh/sshd_config
/etc/init.d/sshd start 
/etc/init.d/sshd stop

echo 'root:pica9' |chpasswd
