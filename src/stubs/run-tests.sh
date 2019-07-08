#!/bin/bash
clear
php -c ./disable-xdebug.ini vendor/bin/phpunit --printer=Sempro\\PHPUnitPrettyPrinter\\PrettyPrinter