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
	 * API request validation.
	 *
	 * @param WP_REST_Request $request request.
	 * @return WP_Error|boolean
	 */
	public static function authenticate( $request ) {
	    // phpcs:ignore
		$public_key = file_get_contents( realpath( plugin_dir_path( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'ca' . DIRECTORY_SEPARATOR . 'publickey' );
		$decrypted  = '';
		if ( openssl_public_decrypt( base64_decode( $request->get_body() ), $decrypted, $public_key ) ) {
			return true;
		}
		return new \WP_Error( 401, 'Could not decrypt request' );
	}
}
