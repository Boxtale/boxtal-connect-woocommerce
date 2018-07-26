#!/usr/bin/env bash

docker cp . boxtal_woocommerce:/home/docker
docker exec -u root boxtal_woocommerce chown -R docker:docker /home/docker
docker exec -u root boxtal_woocommerce chmod -R +x /home/docker/factory/common/test
docker exec -u root boxtal_woocommerce gulp css
docker exec -u root boxtal_woocommerce cp -R node_modules/mapbox-gl/dist/mapbox-gl.css src/Boxtal/BoxtalWoocommerce/assets/css
docker exec -u root boxtal_woocommerce cp -R node_modules/mapbox-gl/dist/mapbox-gl.js src/Boxtal/BoxtalWoocommerce/assets/js
docker exec -u root boxtal_woocommerce gulp js
docker exec -u root boxtal_woocommerce cp -R vendor/boxtal/boxtal-php-poc/src/* src/Boxtal/BoxtalPhp
docker exec -u root boxtal_woocommerce cp -R /var/www/html/wp-content/plugins/boxtal-woocommerce/Boxtal/BoxtalPhp/config.json /tmp
docker exec -u www-data boxtal_woocommerce rm -rf /var/www/html/wp-content/plugins/boxtal-woocommerce
docker exec -u www-data boxtal_woocommerce mkdir /var/www/html/wp-content/plugins/boxtal-woocommerce
docker exec -u www-data boxtal_woocommerce cp -R src/* /var/www/html/wp-content/plugins/boxtal-woocommerce
docker exec -u www-data boxtal_woocommerce cp -R /tmp/config.json /var/www/html/wp-content/plugins/boxtal-woocommerce/Boxtal/BoxtalPhp
docker exec -u www-data boxtal_woocommerce chown -R www-data:www-data /var/www/html/wp-content/plugins/boxtal-woocommerce
docker exec -u www-data boxtal_woocommerce find /var/www/html/wp-content/plugins/boxtal-woocommerce -type d -exec chmod 775 {} \;
docker exec -u www-data boxtal_woocommerce find /var/www/html/wp-content/plugins/boxtal-woocommerce -type f -exec chmod 644 {} \;
