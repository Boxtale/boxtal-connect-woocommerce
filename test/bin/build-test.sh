#!/usr/bin/env bash

WP_VERSION=${1-latest}
WC_VERSION=${2-"2.6.14"}
TRAVIS=${3-false}

DB_NAME=boxtal_woocommerce_test
DB_USER=dbadmin
DB_PASS=dbpass
DB_HOST=localhost
WP_CORE_DIR='/var/www/html'
UNIT_TESTS_DIR='/tmp/unit-tests'
E2E_TESTS_DIR='/tmp/e2e-tests'
WC_DIR='/tmp/woocommerce'

if [ ${TRAVIS} = "false" ]; then
	HOME='/home/docker'
else
	HOME='/home/travis/build/Boxtale/boxtal-woocommerce-poc'
fi

download() {
    if [ `which curl` ]; then
        curl -s "$1" > "$2";
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1"
    fi
}

install_wp() {
    if [ ${TRAVIS} = "false" ]; then
        activate_plugin
		return 0
	fi

	$HOME/build/install-wp.sh $WP_VERSION $WC_VERSION 80
	if [[ $RUN_E2E = "1" ]]; then
        gulp css
        gulp js
        mkdir -p $HOME/src/Boxtal/BoxtalPhp
        cp -R $HOME/vendor/boxtal/boxtal-php-poc/src/* $HOME/src/Boxtal/BoxtalPhp
        mkdir -p /var/www/html/wp-content/plugins/boxtal-woocommerce
        cp -R $HOME/src/* /var/www/html/wp-content/plugins/boxtal-woocommerce
        sudo chown -R www-data:www-data /var/www/html
        sudo find /var/www/html -type d -exec chmod 775 {} \;
        sudo find /var/www/html -type f -exec chmod 644 {} \;
        rm -rf $HOME/src/Boxtal/BoxtalPhp
        activate_plugin
    fi
}

install_db() {
    $HOME/test/bin/reset-unit-test-db.sh
}

install_wc() {
    rm -rf $WC_DIR
    mkdir -p $WC_DIR
    git clone --depth=1 --branch=$WC_VERSION https://github.com/woocommerce/woocommerce.git $WC_DIR
}

install_unit_tests() {
	# portable in-place argument for both GNU sed and Mac OSX sed
	if [[ $(uname -s) == 'Darwin' ]]; then
		local ioption='-i.bak'
	else
		local ioption='-i'
	fi

	# init unit tests folder
	if [ ! -d $UNIT_TESTS_DIR ]; then
		# set up testing suite
		mkdir -p $UNIT_TESTS_DIR
    else
        rm -rf $UNIT_TESTS_DIR
    fi

    # define test tag
    if [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+$ ]]; then
        WP_TESTS_TAG="branches/$WP_VERSION"
    elif [[ $WP_VERSION =~ [0-9]+\.[0-9]+\.[0-9]+ ]]; then
        if [[ $WP_VERSION =~ [0-9]+\.[0-9]+\.[0] ]]; then
            # version x.x.0 means the first release of the major version, so strip off the .0 and download version x.x
            WP_TESTS_TAG="tags/${WP_VERSION%??}"
        else
            WP_TESTS_TAG="tags/$WP_VERSION"
        fi
    elif [[ $WP_VERSION == 'nightly' || $WP_VERSION == 'trunk' ]]; then
        WP_TESTS_TAG="trunk"
    else
        # http serves a single offer, whereas https serves multiple. we only want one
        download http://api.wordpress.org/core/version-check/1.7/ /tmp/wp-latest.json
        grep '[0-9]+\.[0-9]+(\.[0-9]+)?' /tmp/wp-latest.json
        LATEST_VERSION=$(grep -o '"version":"[^"]*' /tmp/wp-latest.json | sed 's/"version":"//')
        if [[ -z "$LATEST_VERSION" ]]; then
            echo "Latest WordPress version could not be found"
            exit 1
        fi
        WP_TESTS_TAG="tags/$LATEST_VERSION"
    fi

    svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/includes/ $UNIT_TESTS_DIR/includes
    svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/data/ $UNIT_TESTS_DIR/data

    download https://develop.svn.wordpress.org/${WP_TESTS_TAG}/wp-tests-config-sample.php "$UNIT_TESTS_DIR"/wp-tests-config.php
    sed $ioption "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR/':" "$UNIT_TESTS_DIR"/wp-tests-config.php
    sed $ioption "s/youremptytestdbnamehere/$DB_NAME/" "$UNIT_TESTS_DIR"/wp-tests-config.php
    sed $ioption "s/yourusernamehere/$DB_USER/" "$UNIT_TESTS_DIR"/wp-tests-config.php
    sed $ioption "s/yourpasswordhere/$DB_PASS/" "$UNIT_TESTS_DIR"/wp-tests-config.php
    sed $ioption "s|localhost|${DB_HOST}|" "$UNIT_TESTS_DIR"/wp-tests-config.php
}

activate_plugin() {
    php wp-cli.phar plugin activate boxtal-woocommerce --allow-root --path=/var/www/html
}

install_wp
install_db
install_wc
install_unit_tests