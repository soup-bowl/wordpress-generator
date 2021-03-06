#! /bin/bash
rm -r out/
cp -r src/ out/
cd out/
rm -r sites/ && rm -r cache/
rm error.log .env .gitignore 
rm -r vendor && rm -r node_modules
composer install --no-dev --ignore-platform-reqs && npm install --production
