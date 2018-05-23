<?php
/**
 * Contains code for api util class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Util
 */

namespace Boxtal\BoxtalWoocommerce\Util;

/**
 * Api util class.
 *
 * Helper to manage API responses.
 *
 * @class       Api_Util
 * @package     Boxtal\BoxtalWoocommerce\Util
 * @category    Class
 * @author      API Boxtal
 */
class Api_Util {

	/**
	 * API request validation.
	 *
	 * @param integer $code http code.
	 * @param mixed   $body to send along response.
	 * @void
	 */
	public static function send_api_response( $code, $body = null ) {
		http_response_code( $code );
		if ($body !== null) {
            // phpcs:ignore
            echo Auth_Util::encrypt_body( $body );
        }
		die();
	}
}
