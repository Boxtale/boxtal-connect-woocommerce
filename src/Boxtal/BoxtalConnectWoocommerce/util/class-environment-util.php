<?php
/**
 * Contains code for environment util class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Util
 */

namespace Boxtal\BoxtalConnectWoocommerce\Util;

use Boxtal\BoxtalConnectWoocommerce\Plugin;
use Boxtal\BoxtalPhp\RestClient;

/**
 * Environment util class.
 *
 * Helper to check environment.
 *
 * @class       Environment_Util
 * @package     Boxtal\BoxtalConnectWoocommerce\Util
 * @category    Class
 * @author      API Boxtal
 */
class Environment_Util {

	/**
	 * Get warning about PHP version, WC version.
	 *
	 * @param Plugin $plugin plugin object.
	 * @return string $message
	 */
	public static function check_errors( $plugin ) {
		if ( false === RestClient::healthcheck() ) {
			return __( 'Boxtal Connect - You need either the curl extension or allow_url_fopen activated on your server for the Boxtal Connect plugin to work.', 'boxtal-connect' );
		}

		if ( version_compare( PHP_VERSION, $plugin['min-php-version'], '<' ) ) {
			/* translators: 1) int version 2) int version */
			$message = __( 'Boxtal Connect - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'boxtal-connect' );

			return sprintf( $message, $plugin['min-php-version'], PHP_VERSION );
		}

		if ( ! defined( 'WC_VERSION' ) ) {
			return __( 'Boxtal Connect requires WooCommerce to be activated to work.', 'boxtal-connect' );
		}

		if ( version_compare( WC_VERSION, $plugin['min-wc-version'], '<' ) ) {
			/* translators: 1) int version 2) int version */
			$message = __( 'Boxtal Connect - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'boxtal-connect' );

			return sprintf( $message, $plugin['min-wc-version'], WC_VERSION );
		}
		return false;
	}
}
