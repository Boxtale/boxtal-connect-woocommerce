#!/usr/bin/env bash

PHP_VERSION=$1
WP_VERSION=$2
WC_VERSION=$3
INCLUDE_LEGACY=${4-false}
PORT=8082
WP_DIR=/var/www/html

set -ex

if [[ (("$INCLUDE_LEGACY" = "false") && ("$(docker images -q 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce:$PHP_VERSION-$WP_VERSION-$WC_VERSION-$PORT  2> /dev/null)" == "")) || (("$INCLUDE_LEGACY" = "true") && ("$(docker images -q 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce-legacy:$PHP_VERSION-$WP_VERSION-$WC_VERSION-$PORT  2> /dev/null)" == "")) ]]; then
    if [[ "$(docker images -q 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce:$PHP_VERSION  2> /dev/null)" == "" ]]; then
        docker build . -t 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce:$PHP_VERSION --build-arg PHP_VERSION=$PHP_VERSION
    fi
    C1=$(docker run -di 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce:$PHP_VERSION)
    docker exec -i $C1 sh -c "sudo sed -i '/Listen 80/a Listen $PORT' /etc/apache2/ports.conf"
    docker exec -i $C1 sh -c "sudo sed -i 's/:80/:$PORT/' /etc/apache2/sites-enabled/000-default.conf"
    docker exec -i $C1 sh -c "/bin/bash ./build/install-wp.sh woocommerce dbadmin dbpass '' localhost $WP_VERSION $WC_VERSION $PORT $INCLUDE_LEGACY"
    if [ "$INCLUDE_LEGACY" = "true" ]; then
        docker commit $C1 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce-legacy:$PHP_VERSION-$WP_VERSION-$WC_VERSION-$PORT
    else
        docker commit $C1 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce:$PHP_VERSION-$WP_VERSION-$WC_VERSION-$PORT
    fi
    docker stop $C1
fi
if [ "$INCLUDE_LEGACY" = "true" ]; then
    C2=$(docker run -di -p $PORT:$PORT 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce-legacy:$PHP_VERSION-$WP_VERSION-$WC_VERSION-$PORT)
else
    C2=$(docker run -di -p $PORT:$PORT 890731937511.dkr.ecr.eu-west-1.amazonaws.com/boxtal-woocommerce:$PHP_VERSION-$WP_VERSION-$WC_VERSION-$PORT)
fi
docker cp src/. $C2:/home/docker/sync/
docker exec -i $C2 sh -c "sudo -u www-data -H sh -c \"cp -R /home/docker/sync/ $WP_DIR/wp-content/plugins/boxtal-woocommerce\""
docker exec -i $C2 sh -c "sudo find $WP_DIR/wp-content/plugins/boxtal-woocommerce -type f -exec chmod 664 {} \;"
docker exec -i $C2 sh -c "sudo find $WP_DIR/wp-content/plugins/boxtal-woocommerce -type d -exec chmod 775 {} \;"
docker exec -i $C2 sh -c "sudo service mysql start"
docker exec -i $C2 sh -c "sudo service apache2 start"
echo "container is running on port $PORT"
echo "site url is http://localhost:$PORT"