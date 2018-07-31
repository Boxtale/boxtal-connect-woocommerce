<?php
/**
 * Bootstrap the plugin unit testing environment.
 *
 * Edit 'active_plugins' setting below to point to your main plugin file.
 *
 * @package wordpress-plugin-tests
 */

$wp_tests_dir = '/tmp/unit-tests';
// load test function so tests_add_filter() is available.
require_once $wp_tests_dir . '/includes/functions.php';

/**
 * Activates this plugin in WordPress so it can be tested.
 */
function _manually_load_plugin() {
	$wc_dir = '/tmp/woocommerce';

	// Load woocommerce plugin.
	require $wc_dir . '/woocommerce.php';

	// Load woocommerce test helpers.
	require $wc_dir . '/tests/framework/helpers/class-wc-helper-product.php';
	require $wc_dir . '/tests/framework/helpers/class-wc-helper-shipping.php';

	// Load boxtal woocommerce plugin.
	require __DIR__ . '/../src/boxtal-woocommerce.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $wp_tests_dir . '/includes/bootstrap.php';

if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
	$_SERVER['SERVER_NAME'] = 'localhost';
}
