#!/usr/bin/env bash

docker build --build-arg PHP_APP_DIR=/var/www/html/ -t suzanne-php5 .