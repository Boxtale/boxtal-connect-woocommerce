#!/usr/bin/env bash

if [ $# -lt 4 ]; then
	echo "usage: $0 <destination> <db-name> <db-user> <db-pass> [db-host] [wp-version] [wc-version]"
	exit 1
fi

set -ex

DEST_DIR=$1
DEST_DIR=$(echo $DEST_DIR | sed -e "s/\/$//")
DB_NAME=$2
DB_USER=$3
DB_PASS=$4
DB_HOST=${5-localhost}
WP_VERSION=${6-latest}
WC_VERSION=${7-3.3.0}

TMPSITEURL="http://localhost/boxtal-woocommerce"
TMPSITETITLE="boxtaltest"
TMPSITEADMINLOGIN="admin"
TMPSITEADMINPWD="admin"
TMPSITEADMINEMAIL="test_wordpress@boxtal.com"
wp='./vendor/wp-cli/wp-cli/bin/wp'
productCsvParser='./build/product-csv-parser.php'

check_requirements() {
 echo 'TO DO check requirements like apache, php, mysql, php extensions'
}

create_directory() {
    WP_CORE_DIR=${WP_CORE_DIR-$DEST_DIR/boxtal-woocommerce}
    sudo rm -rf $WP_CORE_DIR
    sudo -u www-data -H sh -c "mkdir -p $WP_CORE_DIR"
}

download() {
    if [ `which curl` ]; then
        curl -s "$1" > "$2";
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1"
    fi
}

install_wp() {
    sudo -u www-data -H sh -c "$wp core download --force --version=$WP_VERSION --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp core version --path=$WP_CORE_DIR"

    # parse DB_HOST for port or socket references
	local PARTS=(${DB_HOST//\:/ })
	local DB_HOSTNAME=${PARTS[0]};
	local DB_SOCK_OR_PORT=${PARTS[1]};
	local EXTRA=""

	if ! [ -z $DB_HOSTNAME ] ; then
        EXTRA=" --dbhost=$DB_HOSTNAME"
	fi
    sudo -u www-data -H sh -c "$wp core config --dbname=$DB_NAME --dbuser=$DB_USER --dbpass=$DB_PASS $EXTRA --skip-check --path=$WP_CORE_DIR <<PHP
define( 'WP_DEBUG', true );
PHP"

    sudo -u www-data -H sh -c "$wp db reset --yes --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp core install --url=$TMPSITEURL --title=$TMPSITETITLE --admin_user=$TMPSITEADMINLOGIN --admin_email=$TMPSITEADMINEMAIL --admin_password=$TMPSITEADMINPWD --skip-email --path=$WP_CORE_DIR"
}

install_wc() {
    sudo -u www-data -H sh -c "$wp plugin install woocommerce --version=$WC_VERSION --activate --path=$WP_CORE_DIR"
}

set_directory_rights() {
    sudo find $WP_CORE_DIR -type f -exec chmod 664 {} \;
    sudo find $WP_CORE_DIR -type d -exec chmod 775 {} \;
}

install_wc_dummy_data() {
    sudo -u www-data -H sh -c "$wp plugin install wordpress-importer --activate --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp import $WP_CORE_DIR/wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors='create' --path=$WP_CORE_DIR"
    php $productCsvParser $WP_CORE_DIR/wp-content/plugins/woocommerce/sample-data/sample_composproducts.csv
    echo 'product import success'
}

copy_plugin_to_plugin_dir() {
    sudo -u www-data -H sh -c "cp -R src/ $WP_CORE_DIR/wp-content/plugins/boxtal-woocommerce"
}

activate_plugins() {
    echo 'TO DO activate plugins & setup (OPTIONAL)'
}

check_requirements
create_directory
install_wp
install_wc
set_directory_rights
install_wc_dummy_data
copy_plugin_to_plugin_dir
activate_plugins