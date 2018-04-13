<?php
/**
 * Contains code for auth helper class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 */

namespace Boxtal\BoxtalWoocommerce\Helpers;

/**
 * Auth helper class.
 *
 * Helper to manage API auth.
 *
 * @class       Auth_Helper
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 * @category    Class
 * @author      API Boxtal
 */
class Auth_Helper {

	/**
	 * API request token validation.
	 *
	 * @param string $param param value.
	 * @return WP_Error|boolean
	 */
	public static function authenticate( $param ) {
		return get_option( 'BW_API_TOKEN' ) === $param;
	}
}
