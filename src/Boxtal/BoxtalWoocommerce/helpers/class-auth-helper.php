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

	/**
	 * Encrypt key with public key.
	 *
	 * @param string $bw_access_key readable access key.
	 * @return string encrypted key
	 */
	public static function encrypt_key( $bw_access_key ) {
		$encrypted_key = '';
		$pub_key       = self::get_public_key();
		openssl_public_encrypt( $bw_access_key, $encrypted_key, $pub_key, OPENSSL_PKCS1_OAEP_PADDING );
		return $encrypted_key;
	}

	/**
	 * Get public key.
	 *
	 * @return string public key
	 */
	private static function get_public_key() {
        // phpcs:ignore
		$fp      = fopen( realpath( plugin_dir_path( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'ca' . DIRECTORY_SEPARATOR . 'pub_key.pem', 'r' );
        // phpcs:ignore
        $pub_key = fread( $fp, 8192 );
        // phpcs:ignore
		fclose( $fp );
		openssl_pkey_get_public( $pub_key );
		return $pub_key;
	}
}
