#!/bin/bash
set -e

SCRIPT_PATH="`dirname \"$0\"`"
$SCRIPT_PATH/epel.sh
$SCRIPT_PATH/remi.sh
$SCRIPT_PATH/gcc.sh
$SCRIPT_PATH/github.sh
$SCRIPT_PATH/git.sh
$SCRIPT_PATH/os.sh
$SCRIPT_PATH/ssh.sh
$SCRIPT_PATH/supervisord.sh
