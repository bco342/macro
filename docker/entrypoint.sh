#!/bin/bash

/wait-for-it.sh db:3306 -t 30

cd /var/www/app/
php bin/console migrate up

php-fpm
