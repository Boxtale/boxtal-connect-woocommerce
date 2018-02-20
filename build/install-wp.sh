#!/usr/bin/env bash

if [ $# -lt 4 ]; then
	echo "usage: $0 <destination> <db-name> <db-user> <db-pass> [db-host] [wp-version] [wc-version] [port]"
	exit 1
fi

echo "starting install"
set -ex

DEST_DIR=$1
DEST_DIR=$(echo $DEST_DIR | sed -e "s/\/$//")
DB_NAME=$2
DB_USER=$3
DB_PASS=$4
DB_HOST=${5-localhost}
WP_VERSION=${6-latest}
WC_VERSION=${7-3.3.0}

if [ -z "$8" ]; then
    TMPSITEURL="http://localhost/boxtal-woocommerce"
else
    TMPSITEURL="http://localhost:8082/boxtal-woocommerce"
fi
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
    sudo mkdir -p $WP_CORE_DIR
    sudo chown -R www-data:www-data $WP_CORE_DIR
    sudo find $WP_CORE_DIR -type d -exec chmod 775 {} \;
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
    echo 'product import success'
}

wc_setup() {
    sudo -u www-data -H sh -c "$wp option update woocommerce_version $WC_VERSION --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_db_version $WC_VERSION --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_store_address '24 rue Drouot' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_store_city 'Paris' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_store_postcode '75009' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_default_country 'FR' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_currency 'EUR' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_product_type 'both' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_currency_pos 'right' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_price_decimal_sep ',' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_price_num_decimals '2' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_price_thousand_sep ' ' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_allow_tracking 'no' --path=$WP_CORE_DIR"

    SHOP_ID=`sudo -u www-data -H sh -c "$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='shop' --post_title='Shop' --post_content='' --post_parent=0 --comment_status='closed' --porcelain --path=$WP_CORE_DIR"`
    CART_ID=`sudo -u www-data -H sh -c "$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='cart' --post_title='Cart' --post_content='[woocommerce_cart]' --post_parent=0 --comment_status='closed' --porcelain --path=$WP_CORE_DIR"`
    CHECKOUT_ID=`sudo -u www-data -H sh -c "$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='checkout' --post_title='Checkout' --post_content='[woocommerce_checkout]' --post_parent=0 --comment_status='closed' --porcelain --path=$WP_CORE_DIR"`
    MYACCOUNT_ID=`sudo -u www-data -H sh -c "$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='myaccount' --post_title='My account' --post_content='[woocommerce_my_account]' --post_parent=0 --comment_status='closed' --porcelain --path=$WP_CORE_DIR"`
    sudo -u www-data -H sh -c "$wp option update woocommerce_shop_page_id $SHOP_ID --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_cart_page_id $CART_ID --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_checkout_page_id $CHECKOUT_ID --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_myaccount_page_id $MYACCOUNT_ID --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_cheque_settings '{\"enabled\":\"yes\",\"title\":\"Check payments\",\"description\":\"Please send a check to Store Name, Store Street, Store Town, Store State \/ County, Store Postcode.\",\"instructions\":\"\"}' --format=json --autoload='yes' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_calc_taxes 'yes' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp option update woocommerce_setup_automated_taxes 'yes' --path=$WP_CORE_DIR"
    sudo -u www-data -H sh -c "$wp theme install storefront --activate --path=$WP_CORE_DIR"
}

check_requirements
create_directory
install_wp
install_wc
set_directory_rights
install_wc_dummy_data
wc_setup
