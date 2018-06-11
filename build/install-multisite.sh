#!/usr/bin/env bash

echo "starting multisite install"
set -ex

TMPSITETITLE="Boxtal Woocommerce alternate site"
TMPSITEADMINLOGIN="admin"
TMPSITEADMINPWD="admin"
TMPSITEADMINEMAIL="test_wordpress@boxtal.com"
TMPSITESLUG="alternate"
TMPSITEURL="http://localhost"
WP_CORE_DIR=/var/www/html
wp="php wp-cli.phar"

deactivate_plugins() {
    $wp plugin deactivate woocommerce --allow-root --path=$WP_CORE_DIR
    $wp plugin deactivate boxtal-woocommerce --allow-root --path=$WP_CORE_DIR
    $wp plugin deactivate wordpress-importer --allow-root --path=$WP_CORE_DIR
}

core_configuration() {
    sudo chmod 777 $WP_CORE_DIR/wp-config.php
    $wp core multisite-convert --title="$TMPSITETITLE" --base=$TMPSITEURL --allow-root --path=$WP_CORE_DIR
    $wp site create --slug=$TMPSITESLUG --allow-root --path=$WP_CORE_DIR
    sudo chmod 644 $WP_CORE_DIR/wp-config.php
    rm -rf $WP_CORE_DIR/.htaccess
    cp build/multisite-htaccess.txt $WP_CORE_DIR/.htaccess
}

plugin_configuration() {
    $wp plugin activate woocommerce --network --allow-root --path=$WP_CORE_DIR
    $wp plugin activate wordpress-importer --network --allow-root --path=$WP_CORE_DIR

    wc_current_version=`$wp plugin get woocommerce --field=version --allow-root --path=$WP_CORE_DIR`

    switch_version="3.3.0"
    if [ "$(printf '%s\n' "$switch_version" "$wc_current_version" | sort -V | head -n1)" = "$switch_version" ]; then
        $wp import $WP_CORE_DIR/wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors='create' --url=$TMPSITEURL/$TMPSITESLUG --allow-root --path=$WP_CORE_DIR
    else
        $wp import $WP_CORE_DIR/wp-content/plugins/woocommerce/dummy-data/dummy-data.xml --authors='create' --url=$TMPSITEURL/$TMPSITESLUG --allow-root --path=$WP_CORE_DIR
    fi

    $wp option update woocommerce_store_address '24 rue Drouot' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_store_city 'Paris' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_store_postcode '75009' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_default_country 'FR' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_currency 'EUR' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_product_type 'both' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_currency_pos 'right' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_price_decimal_sep ',' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_price_num_decimals '2' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_price_thousand_sep ' ' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_allow_tracking 'no' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG

    SHOP_ID=`$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='shop' --post_title='Shop' --post_content='' --post_parent=0 --comment_status='closed' --porcelain --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG`
    CART_ID=`$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='cart' --post_title='Cart' --post_content='[woocommerce_cart]' --post_parent=0 --comment_status='closed' --porcelain --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG`
    CHECKOUT_ID=`$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='checkout' --post_title='Checkout' --post_content='[woocommerce_checkout]' --post_parent=0 --comment_status='closed' --porcelain --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG`
    MYACCOUNT_ID=`$wp post create --post_status=publish --post_type=page --post_author=1 --post_name='myaccount' --post_title='My account' --post_content='[woocommerce_my_account]' --post_parent=0 --comment_status='closed' --porcelain --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG`
    $wp option update woocommerce_shop_page_id $SHOP_ID --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_cart_page_id $CART_ID --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_checkout_page_id $CHECKOUT_ID --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_myaccount_page_id $MYACCOUNT_ID --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_cheque_settings '{"enabled":"yes","title":"Check payments","description":"Please send a check to Store Name, Store Street, Store Town, Store State / County, Store Postcode.","instructions":""}' --format=json --autoload='yes' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_calc_taxes 'yes' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp option update woocommerce_setup_automated_taxes 'yes' --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
    $wp theme enable storefront --network --activate --allow-root --path=$WP_CORE_DIR --url=$TMPSITEURL/$TMPSITESLUG
}

deactivate_plugins
core_configuration
plugin_configuration

