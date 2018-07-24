<?php
/**
 * Contains code for environment util class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Util
 */

namespace Boxtal\BoxtalWoocommerce\Util;

use Boxtal\BoxtalWoocommerce\Plugin;

/**
 * Environment util class.
 *
 * Helper to check environment
 *
 * @class       Environment_Util
 * @package     Boxtal\BoxtalWoocommerce\Util
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
    public static function check_errors($plugin) {
        if ( version_compare( PHP_VERSION, $plugin['min-php-version'], '<' ) ) {
            /* translators: 1) int version 2) int version */
            $message = __( 'Boxtal Woocommerce - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'boxtal-woocommerce' );

            return sprintf( $message, $plugin['min-php-version'], PHP_VERSION );
        }

        if ( ! defined( 'WC_VERSION' ) ) {
            return __( 'Boxtal Woocommerce requires WooCommerce to be activated to work.', 'boxtal-woocommerce' );
        }

        if ( version_compare( WC_VERSION, $plugin['min-wc-version'], '<' ) ) {
            /* translators: 1) int version 2) int version */
            $message = __( 'Boxtal Woocommerce - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'boxtal-woocommerce' );

            return sprintf( $message, $plugin['min-wc-version'], WC_VERSION );
        }
        return false;
    }
}
