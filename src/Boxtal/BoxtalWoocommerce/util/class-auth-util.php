<?php
/**
 * Contains code for auth util class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Util
 */

namespace Boxtal\BoxtalWoocommerce\Util;

/**
 * Auth util class.
 *
 * Helper to manage API auth.
 *
 * @class       Auth_Util
 * @package     Boxtal\BoxtalWoocommerce\Util
 * @category    Class
 * @author      API Boxtal
 */
class Auth_Util {

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

	/**
	 * Is plugin paired.
	 *
	 * @return boolean
	 */
	public static function is_plugin_paired() {
		return 'yes' === get_option( 'BW_PLUGIN_SETUP' );
	}

	/**
	 * Pair plugin.
	 *
	 * @void
	 */
	public static function pair_plugin() {
		update_option( 'BW_PLUGIN_SETUP', 'yes' );
	}
}
