#! /bin/ash
nginx
fcgiwrap -s tcp:127.0.0.1:9001 &
php-fpm
