<?php
/**
 * Contains code for shipping rate util class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Util
 */

namespace Boxtal\BoxtalWoocommerce\Util;

/**
 * Shipping rate util class.
 *
 * Helper to manage consistency between woocommerce versions shipping rate getters and setters.
 *
 * @class       Shipping_Rate_Util
 * @package     Boxtal\BoxtalWoocommerce\Util
 * @category    Class
 * @author      API Boxtal
 */
class Shipping_Rate_Util {

	/**
	 * Get shipping method settings from shipping rate.
	 *
	 * @param WC_Shipping_Rate $method woocommerce shipping rate.
	 * @return array $settings shipping rate settings
	 */
	public static function get_settings( $method ) {
        return get_option( self::get_settings_key($method) );
	}

    /**
     * Get shipping method settings key from shipping rate.
     *
     * @param WC_Shipping_Rate $method woocommerce shipping rate.
     * @return string $settings_key shipping rate settings key
     */
	private static function get_settings_key($method) {
	    list($method_name, $method_instance_id) = explode(':', $method->id);
        return 'woocommerce_' . $method_name . '_' . $method_instance_id . '_settings';
    }
}
