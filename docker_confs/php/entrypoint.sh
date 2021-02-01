#!/usr/bin/env bash

composer install --ignore-platform-reqs --no-interaction

npm install --global yarn

yarn install

yarn add bootstrap --dev

yarn add jquery popper.js --dev

yarn add sass-loader@^10.0.0 sass --dev

php-fpm