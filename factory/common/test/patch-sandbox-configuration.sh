#!/usr/bin/env bash

echo "patching sandbox configuration"
set -ex

MULTISITE=${1-0}

WP_CORE_DIR='/var/www/html'
MULTISITE_PRIMARY_URL='http://localhost'
MULTISITE_ALTERNATE_URL='http://localhost/alternate'
wp="php wp-cli.phar"
ACCESS_KEY='T9LO82EWMOQ61G4272TB'
SECRET_KEY='6e97724f-f176-4def-8195-8c7fea49794c'
SANDBOX_MAP_BOOTSTRAP_URL='https://maps.boxtal.build/styles/boxtal/style.json?access_token=${access_token}'
SANDBOX_MAP_TOKEN_URL='https://api.boxtal.build/v2/token/maps'
PP_NETWORKS='O:8:"stdClass":4:{s:12:"CHRP_NETWORK";a:1:{i:0;s:10:"Chronopost";}s:12:"SOGP_NETWORK";a:1:{i:0;s:12:"Relais colis";}s:12:"UPSE_NETWORK";a:1:{i:0;s:3:"UPS";}s:12:"MONR_NETWORK";a:4:{i:0;s:13:"Mondial Relay";i:1;s:10:"Happy Post";i:2;s:10:"Punto Pack";i:3;s:20:"Boxtal Mondial Relay";}}'

SANDBOX_API_URL='https://api.boxtal.build'
ESCAPED_APIURL=$(sed 's|/|\\/|g' <<< $SANDBOX_API_URL)
sudo -u www-data -H sh -c "sed -i \"s/apiUrl\\\": \\\"https:\/\/api.boxtal.org\\\"/apiUrl\\\": \\\"$ESCAPED_APIURL\\\"/\"  $WP_CORE_DIR/wp-content/plugins/boxtal-connect/Boxtal/BoxtalPhp/config.json"
sudo -u www-data -H sh -c "sed -i \"s/apiUrl\\\": \\\"https:\/\/api.boxtal.com\\\"/apiUrl\\\": \\\"$ESCAPED_APIURL\\\"/\"  $WP_CORE_DIR/wp-content/plugins/boxtal-connect/Boxtal/BoxtalPhp/config.json"

if [[ $MULTISITE = "1" ]]; then
    $wp option update BW_ACCESS_KEY $ACCESS_KEY --allow-root --path=$WP_CORE_DIR --url=$MULTISITE_PRIMARY_URL
    $wp option update BW_SECRET_KEY $SECRET_KEY --allow-root --path=$WP_CORE_DIR --url=$MULTISITE_PRIMARY_URL
    $wp option update BW_MAP_BOOTSTRAP_URL $SANDBOX_MAP_BOOTSTRAP_URL --allow-root --path=$WP_CORE_DIR --url=$MULTISITE_PRIMARY_URL
    $wp option update BW_MAP_TOKEN_URL $SANDBOX_MAP_TOKEN_URL --allow-root --path=$WP_CORE_DIR --url=$MULTISITE_PRIMARY_URL
    mysql -u dbadmin -pdbpass -D "woocommerce" -e "INSERT INTO wp_options (option_name, option_value) VALUES ('BW_PP_NETWORKS', '$PP_NETWORKS') ON DUPLICATE KEY UPDATE option_value='$PP_NETWORKS';"
    $wp option update BW_ACCESS_KEY $ACCESS_KEY --allow-root --path=$WP_CORE_DIR --url=$MULTISITE_ALTERNATE_URL
    $wp option update BW_SECRET_KEY $SECRET_KEY --allow-root --path=$WP_CORE_DIR --url=$MULTISITE_ALTERNATE_URL
    $wp option update BW_MAP_BOOTSTRAP_URL $SANDBOX_MAP_BOOTSTRAP_URL --allow-root --path=$WP_CORE_DIR --url=$MULTISITE_ALTERNATE_URL
    $wp option update BW_MAP_TOKEN_URL $SANDBOX_MAP_TOKEN_URL --allow-root --path=$WP_CORE_DIR --url=$MULTISITE_ALTERNATE_URL
    mysql -u dbadmin -pdbpass -D "woocommerce" -e "INSERT INTO wp_2_options (option_name, option_value) VALUES ('BW_PP_NETWORKS', '$PP_NETWORKS') ON DUPLICATE KEY UPDATE option_value='$PP_NETWORKS';"
else
    $wp option update BW_ACCESS_KEY $ACCESS_KEY --allow-root --path=$WP_CORE_DIR
    $wp option update BW_SECRET_KEY $SECRET_KEY --allow-root --path=$WP_CORE_DIR
    $wp option update BW_MAP_BOOTSTRAP_URL $SANDBOX_MAP_BOOTSTRAP_URL --allow-root --path=$WP_CORE_DIR
    $wp option update BW_MAP_TOKEN_URL $SANDBOX_MAP_TOKEN_URL --allow-root --path=$WP_CORE_DIR
    mysql -u dbadmin -pdbpass -D "woocommerce" -e "INSERT INTO wp_options (option_name, option_value) VALUES ('BW_PP_NETWORKS', '$PP_NETWORKS') ON DUPLICATE KEY UPDATE option_value='$PP_NETWORKS';"
fi
