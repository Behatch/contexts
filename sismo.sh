#!/bin/bash

set -e

pids=()

trap 'kill ${pids[@]} 2> /dev/null' EXIT

Xvfb :99 -screen 0 1024x768x8 2> /dev/null &
pids+=($!)

wget -q http://selenium.googlecode.com/files/selenium-server-standalone-2.31.0.jar -O selenium.jar
DISPLAY=:99.0 java -jar selenium.jar > /dev/null &
sleep 10
pids+=($!)

php -S localhost:8080 -t fixtures/www 2> /dev/null &
pids+=($!)

wget -q http://getcomposer.org/installer -O - | php;
./composer.phar install --dev;
cp behat.yml{-dist,}

./bin/behat -fprogress --tags='~@user'
./bin/atoum
