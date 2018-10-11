<?php
/**
 * Contains code for api util class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Util
 */

namespace Boxtal\BoxtalConnectWoocommerce\Util;

use Boxtal\BoxtalConnectWoocommerce\Plugin;

/**
 * Api util class.
 *
 * Helper to manage API responses.
 *
 * @class       Api_Util
 * @package     Boxtal\BoxtalConnectWoocommerce\Util
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
		$boxtal_connect = Plugin::getInstance();
		header( 'X-Version: ' . $boxtal_connect['version'] );
		http_response_code( $code );
		if ( null !== $body ) {
            // phpcs:ignore
            echo Auth_Util::encrypt_body( $body );
		}
		die();
	}
}
