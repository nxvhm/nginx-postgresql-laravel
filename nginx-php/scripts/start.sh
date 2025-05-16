#!/bin/bash


mkdir -p -m 0700 /root/.ssh
# Prevent config files from being filled to infinity by force of stop and restart the container
echo "" > /root/.ssh/config
echo -e "Host *\n\tStrictHostKeyChecking no\n" >> /root/.ssh/config

# Start supervisord and services
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
