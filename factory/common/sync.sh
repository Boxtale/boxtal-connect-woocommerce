#!/usr/bin/env bash

set -ex

sudo chown -R www-data:www-data /var/www/html
npm install
sudo node_modules/gulp/bin/gulp.js css
sudo node_modules/gulp/bin/gulp.js js
sudo rm -rf src/Boxtal/BoxtalPhp
sudo mkdir -p src/Boxtal/BoxtalPhp
sudo cp -R vendor/boxtal/boxtal-php-poc/src/* src/Boxtal/BoxtalPhp
sudo -H -u www-data bash -c "rm -rf /var/www/html/wp-content/plugins/boxtal-connect"
sudo -H -u www-data bash -c "mkdir -p /var/www/html/wp-content/plugins/boxtal-connect"
sudo -H -u www-data bash -c "cp -R src/* /var/www/html/wp-content/plugins/boxtal-connect"
sudo -H -u www-data bash -c "chown -R www-data:www-data /var/www/html/wp-content/plugins/boxtal-connect"
sudo -H -u www-data bash -c "find /var/www/html/wp-content/plugins/boxtal-connect -type d -exec chmod 775 {} \;"
sudo -H -u www-data bash -c "find /var/www/html/wp-content/plugins/boxtal-connect -type f -exec chmod 644 {} \;"
