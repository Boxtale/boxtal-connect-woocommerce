<?php
/**
 * Contains code for shipping rate helper class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 */

namespace Boxtal\BoxtalWoocommerce\Helpers;

/**
 * Shipping rate helper class.
 *
 * Helper to manage consistency between woocommerce versions shipping rate getters and setters.
 *
 * @class       Shipping_Rate_Helper
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 * @category    Class
 * @author      API Boxtal
 */
class Shipping_Rate_Helper {

	/**
	 * Get shipping method settings from shipping rate.
	 *
	 * @param WC_Shipping_Rate $method woocommerce shipping rate.
	 * @return array $settings shipping rate settings
	 */
	public static function get_settings( $method ) {
		$settings_key = $method->instance_id ? 'woocommerce_' . $method->method_id . '_' . $method->instance_id . '_settings' : 'woocommerce_' . $method->method_id . '_settings';
		return get_option( $settings_key );
	}
}
