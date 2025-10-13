#!/usr/bin/env bash
set -e
# install calendar extension if missing
php -r "exit(extension_loaded('calendar') ? 0 : 1)" || install-php-extensions calendar
# run migrations and seed the database
php artisan migrate:fresh --force --seed