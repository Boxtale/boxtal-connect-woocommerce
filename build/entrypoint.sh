#!/usr/bin/env bash

sudo service mysql start
sudo a2enmod rewrite
sudo service apache2 start

if ! [ -z "$WP_SITEURL" ]; then
    wp='php wp-cli.phar'
    WP_CORE_DIR=/var/www/html
    sudo -u www-data -H sh -c "$wp option update siteurl $WP_SITEURL --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update home $WP_SITEURL --path=$WP_CORE_DIR"

    if [[ $WP_SITEURL = *"https"* && -f $WP_CORE_DIR/wp-config.php ]]; then
        sudo -u www-data -H sh -c "sed -i \"/wp_';/a if (\\\$_SERVER[\\\"HTTP_X_FORWARDED_PROTO\\\"] === \\\"https\\\") {\n \\\$_SERVER[\\\"HTTPS\\\"] = \\\"1\\\"; \n } \n\n if (isset(\\\$_SERVER[\\\"HTTP_X_FORWARDED_HOST\\\"])) { \n \\\$_SERVER[\\\"HTTP_HOST\\\"] = \\\$_SERVER[\\\"HTTP_X_FORWARDED_HOST\\\"]; \n }\" $WP_CORE_DIR/wp-config.php"
    fi
fi

while true; do
	tail -f /dev/null & wait ${!}
done
