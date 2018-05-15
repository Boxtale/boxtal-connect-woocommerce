#!/usr/bin/env bash

echo "starting wordpress install"
set -ex

WP_VERSION=${1-latest}
WC_VERSION=${2-3.3.5}
PORT=${3-80}

if [ $PORT = "80" ]; then
    TMPSITEURL="http://localhost"
else
    TMPSITEURL="http://localhost:$PORT"
fi

TMPSITETITLE="Boxtal Woocommerce test site"
TMPSITEADMINLOGIN="admin"
TMPSITEADMINPWD="admin"
TMPSITEADMINEMAIL="test_wordpress@boxtal.com"
WP_CORE_DIR=/var/www/html
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
wp="php wp-cli.phar"

clean_wp_dir() {
    sudo rm $WP_CORE_DIR/index.html
}

install_wp() {
    $wp core download --force --version=$WP_VERSION --allow-root --path=$WP_CORE_DIR
    $wp core version --allow-root --path=$WP_CORE_DIR
    $wp core config --dbname=woocommerce --dbuser=dbadmin --dbpass=dbpass --skip-check --allow-root --path=$WP_CORE_DIR --dbprefix=wp_ --extra-php <<PHP
/* Change WP_MEMORY_LIMIT to increase the memory limit for public pages. */
define('WP_MEMORY_LIMIT', '256M');

/* Activate debug. */
define( 'WP_DEBUG', true );
PHP
    $wp db reset --yes --allow-root --path=$WP_CORE_DIR
    $wp core install --url=$TMPSITEURL --title="$TMPSITETITLE" --admin_user=$TMPSITEADMINLOGIN --admin_email=$TMPSITEADMINEMAIL --admin_password=$TMPSITEADMINPWD --skip-email --allow-root --path=$WP_CORE_DIR
}

install_wc() {
    $wp plugin install woocommerce --version=$WC_VERSION --activate --allow-root --path=$WP_CORE_DIR
}

install_wc_dummy_data() {
    $wp plugin install wordpress-importer --activate --allow-root --path=$WP_CORE_DIR
    $wp import $WP_CORE_DIR/wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors='create' --allow-root --path=$WP_CORE_DIR
    echo 'product import success'
}

wc_setup() {
    $wp option update woocommerce_version $WC_VERSION --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_db_version $WC_VERSION --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_store_address '24 rue Drouot' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_store_city 'Paris' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_store_postcode '75009' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_default_country 'FR' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_currency 'EUR' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_product_type 'both' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_currency_pos 'right' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_price_decimal_sep ',' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_price_num_decimals '2' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_price_thousand_sep ' ' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_allow_tracking 'no' --allow-root --path=$WP_CORE_DIR

    SHOP_ID=`$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='shop' --post_title='Shop' --post_content='' --post_parent=0 --comment_status='closed' --porcelain --allow-root --path=$WP_CORE_DIR`
    CART_ID=`$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='cart' --post_title='Cart' --post_content='[woocommerce_cart]' --post_parent=0 --comment_status='closed' --porcelain --allow-root --path=$WP_CORE_DIR`
    CHECKOUT_ID=`$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='checkout' --post_title='Checkout' --post_content='[woocommerce_checkout]' --post_parent=0 --comment_status='closed' --porcelain --allow-root --path=$WP_CORE_DIR`
    MYACCOUNT_ID=`$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='myaccount' --post_title='My account' --post_content='[woocommerce_my_account]' --post_parent=0 --comment_status='closed' --porcelain --allow-root --path=$WP_CORE_DIR`
    $wp option update woocommerce_shop_page_id $SHOP_ID --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_cart_page_id $CART_ID --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_checkout_page_id $CHECKOUT_ID --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_myaccount_page_id $MYACCOUNT_ID --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_cheque_settings '{"enabled":"yes","title":"Check payments","description":"Please send a check to Store Name, Store Street, Store Town, Store State / County, Store Postcode.","instructions":""}' --format=json --autoload='yes' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_calc_taxes 'yes' --allow-root --path=$WP_CORE_DIR
    $wp option update woocommerce_setup_automated_taxes 'yes' --allow-root --path=$WP_CORE_DIR
    $wp theme install storefront --activate --allow-root --path=$WP_CORE_DIR
}

clean_wp_dir
install_wp
install_wc
install_wc_dummy_data
wc_setup
