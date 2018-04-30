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
    rm -rf $E2E_TEST_DIR
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
    mkdir $E2E_TEST_DIR
    cp -R ./test/e2e/. $E2E_TEST_DIR
    cd $E2E_TEST_DIR
    export NVM_DIR="$HOME/.nvm"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
    nvm install node
    npm install
}

clean_directories
drop_test_database
install_wc
install_wp
install_e2e_tests