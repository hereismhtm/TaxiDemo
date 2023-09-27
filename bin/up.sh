#!/bin/sh

docker compose up -d

CFG=/var/www/html/src/application/config/config.php
RAND=$(cat /dev/urandom | tr -dc '[:alpha:]' | fold -w ${1:-20} | head -n 1)

docker exec taxidemo-backend-container sh -c "sed -i "s/{RandomSecretValue}/$RAND/" $CFG"
