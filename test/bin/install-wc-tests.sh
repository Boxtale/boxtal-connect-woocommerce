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

clean_directories
drop_test_database
install_wc
install_wp