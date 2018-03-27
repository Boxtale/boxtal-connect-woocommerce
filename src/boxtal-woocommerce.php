<?php
/**
 * Plugin Name: Boxtal Woocommerce
 * Plugin URI: https://github.com/boxtal/boxtal-woocommerce-poc
 * Description: Manage multiple carriers using one single plugin and reduce your shipping costs without commitments or any contract to sign.
 * Author: API Boxtal
 * Author URI: https://www.boxtal.com
 * Text Domain: boxtal
 * Domain Path: /languages
 * Version: 0.1.0
 *
 * @package Boxtal\BoxtalWoocommerce
 */

use Boxtal\BoxtalWoocommerce\Plugin;
use Boxtal\BoxtalWoocommerce\Api\Order_Sync;

if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

require_once trailingslashit( __DIR__ ) . 'Boxtal/BoxtalWoocommerce/autoload/autoloader.php';

add_action( 'plugins_loaded', 'boxtal_woocommerce_init' );
/**
 * Plugin initialization.
 *
 * @void
 */
function boxtal_woocommerce_init() {
	$plugin                      = new Plugin(); // Create container.
	$plugin['path']              = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
	$plugin['url']               = plugin_dir_url( __FILE__ );
	$plugin['version']           = '0.1.0';
	$plugin['min-wc-version']    = '2.3.0';
	$plugin['min-php-version']   = '5.3.0';
	$plugin['check-environment'] = 'boxtal_woocommerce_check_environment';
	$plugin['api-order-sync']    = 'boxtal_woocommerce_service_api_order_sync';
	$plugin->run();
}

add_action( 'admin_init', 'boxtal_woocommerce_check_environment' );
/**
 * Check PHP version, WC version.
 *
 * @param array $plugin plugin array.
 * @void
 */
function boxtal_woocommerce_check_environment( $plugin ) {

	$environment_warning = boxtal_woocommerce_get_environment_warning( $plugin );

	/*
	 * Implement notices.
	 * if ( $environment_warning && is_plugin_active( plugin_basename( __FILE__ ) ) ) {
	 * 	add notice.
	 * }
	 */
}

/**
 * Get warning about PHP version, WC version.
 *
 * @param array $plugin plugin array.
 * @return string $message
 */
function boxtal_woocommerce_get_environment_warning( $plugin ) {
	if ( version_compare( phpversion(), $plugin['min-php-version'], '<' ) ) {
		/* translators: 1) int version 2) int version */
		$message = __( 'Boxtal - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'boxtal-woocommerce' );

		return sprintf( $message, $plugin['min-php-version'], phpversion() );
	}

	if ( ! defined( 'WC_VERSION' ) ) {
		return __( 'Boxtal requires WooCommerce to be activated to work.', 'boxtal-woocommerce' );
	}

	if ( version_compare( WC_VERSION, $plugin['min-php-version'], '<' ) ) {
		/* translators: 1) int version 2) int version */
		$message = __( 'Boxtal - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'boxtal-woocommerce' );

		return sprintf( $message, $plugin['min-php-version'], WC_VERSION );
	}
}

/**
 * Get new Order_Sync instance.
 *
 * @return Order_Sync $order_sync
 */
function boxtal_woocommerce_service_api_order_sync() {
	return new Order_Sync();
}
