#! /bin/bash
cd src
rm -r vendor && rm -r node_modules
composer install && npm install
