#!/usr/bin/env bash

sudo service mysql start
sudo service apache2 start

if [ -z "$WP_SITEURL" ]; then
    echo "define('WP_HOME','$WP_SITEURL');" >> /var/www/html/wp-config.php
    echo "define('WP_SITEURL','$WP_SITEURL');" >> /var/www/html/wp-config.php
fi

while true; do
	tail -f /dev/null & wait ${!}
done
