#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version] [wc-version]"
	exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}
WC_VERSION=${6-"2.6.14"}
E2E_TEST_DIR='/tmp/wc-e2e-page-objects-tutorial'

clean_directories() {
    rm -rf /tmp/woocommerce
    rm -rf /tmp/wordpress
    rm -rf /tmp/wordpress-tests-lib
}

drop_test_database() {
    mysqladmin drop -f $DB_NAME --user="$DB_USER" --password="$DB_PASS"
}

install_wc() {
	git clone --depth=1 --branch=$WC_VERSION https://github.com/woocommerce/woocommerce.git /tmp/woocommerce
}

install_wp() {
	bash /tmp/woocommerce/tests/bin/install.sh $DB_NAME $DB_USER "$DB_PASS" $DB_HOST $WP_VERSION
}

install_e2e_tests() {
    # Script Variables
    CONFIG_DIR="./test/e2e/config/travis"
    WP_CORE_DIR="$HOME/wordpress"
    NGINX_DIR="$HOME/nginx"
    PHP_FPM_BIN="$HOME/.phpenv/versions/$TRAVIS_PHP_VERSION/sbin/php-fpm"
    PHP_FPM_CONF="$NGINX_DIR/php-fpm.conf"
    WP_SITE_URL="http://localhost:8080"
    BRANCH=$TRAVIS_BRANCH
    REPO=$TRAVIS_REPO_SLUG
    WORKING_DIR="$PWD"
    BW_DIR="/tmp/bw"

    if [ "$TRAVIS_PULL_REQUEST_BRANCH" != "" ]; then
        BRANCH=$TRAVIS_PULL_REQUEST_BRANCH
        REPO=$TRAVIS_PULL_REQUEST_SLUG
    fi

    set -ex
    npm install --prefix test/e2e
    export NODE_CONFIG_DIR="./test/e2e-test/config"

    # Set up nginx to run the server
    mkdir -p "$WP_CORE_DIR"
    mkdir -p "$NGINX_DIR"
    mkdir -p "$NGINX_DIR/sites-enabled"
    mkdir -p "$NGINX_DIR/var"
    mkdir -p "tpm/nginx-logs"

    cp "$CONFIG_DIR/travis_php-fpm.conf" "$PHP_FPM_CONF"

    # Start php-fpm
    "$PHP_FPM_BIN" --fpm-config "$PHP_FPM_CONF"

    # Copy the default nginx config files.
    cp "$CONFIG_DIR/travis_nginx.conf" "$NGINX_DIR/nginx.conf"
    cp "$CONFIG_DIR/travis_fastcgi.conf" "$NGINX_DIR/fastcgi.conf"
    cp "$CONFIG_DIR/travis_default-site.conf" "$NGINX_DIR/sites-enabled/default-site.conf"


    find /tmp/nginx-logs -type d -exec chmod 766 {} \;

    # Start nginx.
    nginx -c "$NGINX_DIR/nginx.conf"

    # Set up WordPress using wp-cli
    cd "$WP_CORE_DIR"

    curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
    php wp-cli.phar core download --version=$WP_VERSION
    php wp-cli.phar core config --dbname=$DB_NAME --dbuser=$DB_USER --dbpass=$DB_PASS --dbhost=$DB_HOST --dbprefix=wp_ --extra-php <<PHP
/* Change WP_MEMORY_LIMIT to increase the memory limit for public pages. */
define('WP_MEMORY_LIMIT', '256M');
PHP
    php wp-cli.phar core install --url="$WP_SITE_URL" --title="Example" --admin_user=admin --admin_password=admin --admin_email=info@example.com --path=$WP_CORE_DIR --skip-email
    php wp-cli.phar search-replace "http://local.wordpress.test" "$WP_SITE_URL"
    php wp-cli.phar theme install twentytwelve --activate

    git clone https://github.com/$REPO.git $BW_DIR

    cd "$BW_DIR"
    npm install
    npm install -g gulp-cli
    gulp css
    gulp js
    zip -r boxtal-woocommerce.zip src/.

    cd "$WP_CORE_DIR"
    php wp-cli.phar plugin install $BW_DIR/boxtal-woocommerce.zip --activate

    cd "$WORKING_DIR"
}

clean_directories
drop_test_database
install_wc
install_wp
install_e2e_tests