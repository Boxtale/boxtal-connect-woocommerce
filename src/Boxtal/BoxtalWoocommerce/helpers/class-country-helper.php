<?php
/**
 * Contains code for country helper class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 */

namespace Boxtal\BoxtalWoocommerce\Helpers;

/**
 * Country helper class.
 *
 * Helper to manage consistency between woocommerce versions country getters and setters.
 *
 * @class       Country_Helper
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 * @category    Class
 * @author      API Boxtal
 */
class Country_Helper {

	/**
	 * Get activated countries.
	 *
	 * @return array $activated_countries activated countries
	 */
	public static function get_activated_countries() {
		static $activated_countries;

		if ( null !== $activated_countries ) {
			return $activated_countries;
		}

		$activated_countries = new \WC_Countries();
		return $activated_countries;
	}
}
