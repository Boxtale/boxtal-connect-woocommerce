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
	 * @param \WP_REST_Request $request request.
	 * @return \WP_Error|boolean
	 */
	public static function authenticate( $request ) {
        // phpcs:ignore
        $public_key = file_get_contents(realpath(plugin_dir_path(__DIR__)) . DIRECTORY_SEPARATOR . 'resource' . DIRECTORY_SEPARATOR . 'publickey');
		$decrypted  = '';
		if ( openssl_public_decrypt( base64_decode( $request->get_body() ), $decrypted, $public_key ) ) {
			return true;
		}
		return Api_Util::send_api_response( 401 );
	}

	/**
	 * Is plugin paired.
	 *
	 * @return boolean
	 */
	public static function is_plugin_paired() {
		return false !== get_option( 'BW_ACCESS_KEY' ) && false !== get_option( 'BW_SECRET_KEY' );
	}

	/**
	 * Pair plugin.
	 *
	 * @param string $access_key API access key.
	 * @param string $secret_key API secret key.
	 * @void
	 */
	public static function pair_plugin( $access_key, $secret_key ) {
		update_option( 'BW_ACCESS_KEY', $access_key );
		update_option( 'BW_SECRET_KEY', $secret_key );
	}

	/**
	 * Request body decryption.
	 *
	 * @param string $body encrypted body.
	 * @return mixed
	 */
	public static function decrypt_body( $body ) {
        // phpcs:ignore
        $public_key = file_get_contents(realpath(plugin_dir_path(__DIR__)) . DIRECTORY_SEPARATOR . 'resource' . DIRECTORY_SEPARATOR . 'publickey');
		$decrypted  = '';
		if ( openssl_public_decrypt( base64_decode( $body ), $decrypted, $public_key ) ) {
			return json_decode( $decrypted );
		}
		return null;
	}

	/**
	 * Request body decryption.
	 *
	 * @param mixed $body encrypted body.
	 * @return mixed
	 */
	public static function encrypt_body( $body ) {
        // phpcs:ignore
        $public_key = file_get_contents(realpath(plugin_dir_path(__DIR__)) . DIRECTORY_SEPARATOR . 'resource' . DIRECTORY_SEPARATOR . 'publickey');
		$encrypted  = '';
		if ( is_array( $body ) ) {
			$body = wp_json_encode( $body );
		}
		if ( openssl_public_encrypt( $body, $encrypted, $public_key, OPENSSL_PKCS1_OAEP_PADDING ) ) {
			return $encrypted;
		}
		return null;
	}
}
