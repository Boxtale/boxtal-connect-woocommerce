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

namespace Boxtal\BoxtalWoocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if WooCommerce is active (include network check)
 */
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

if ( ! class_exists( 'Boxtal_Woocommerce' ) ) {

	require 'autoload/boxtal-woocommerce-autoloader.php';

	define( 'EMC_LOG_FILE', 'Boxtal_Woocommerce' );

	// Setup activation, deactivation and uninstall hooks.
	register_activation_hook( __FILE__, array( 'Boxtal_Woocommerce', 'activate_simple' ) );
	register_deactivation_hook( __FILE__, array( 'Boxtal_Woocommerce', 'deactivate_simple' ) );
	register_uninstall_hook( __FILE__, array( 'Boxtal_Woocommerce', 'uninstall_simple' ) );

	/**
	 * Boxtal Woocommerce main class
	 *
	 * Handles plugin installation & instantiation.
	 *
	 * @class       Boxtal_Woocommerce
	 * @version     0.1.0
	 * @package     Boxtal/BoxtalWooCommerce
	 * @category    Class
	 * @author      API Boxtal
	 */
	class Boxtal_Woocommerce {

		/**
		 * Protected instance for the plugin
		 *
		 * @static
		 * @var Boxtal_Woocommerce
		 */
		protected static $_instance = null;

		/**
		 * Main boxtal woocommerce Instance
		 *
		 * Ensures only one instance of boxtal woocommerce is loaded or can be loaded.
		 *
		 * @static
		 * @see boxtal_woocommerce()
		 * @return Boxtal_Woocommerce - Main instance
		 */
		public static function instance() {
			if ( null === self::$_instance ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Activate the module, install it if its tables do not exist
		 */
		public static function activate_simple() {
		}

		/**
		 * Deactivate the module and flush the offers cache
		 */
		public static function deactivate_simple() {
		}

		/**
		 * Remove completely the plugin from woocommerce
		 */
		public static function uninstall_simple() {
		}

		/**
		 * Say hello to the world
		 */
		public function hello() {
			return 'hello world';
		}

	}
}

/**
 * Returns the main instance of Boxtal_Woocommerce.
 *
 * @return Boxtal_Woocommerce::instance
 */
function boxtal_woocommerce() {
	return Boxtal_Woocommerce::instance();
}

boxtal_woocommerce();
