<?php
/**
 * Contains code for the shop notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Notices
 */

namespace Boxtal\BoxtalWoocommerce\Notices;

use Boxtal\BoxtalWoocommerce\Abstracts\Notice;
use Boxtal\BoxtalWoocommerce\Admin\Notices;

/**
 * Shop notice class.
 *
 * Shop notice enables pairing with boxtal.
 *
 * @class       Shop_Notice
 * @package     Boxtal\BoxtalWoocommerce\Notices
 * @category    Class
 * @author      API Boxtal
 */
class Shop_Notice extends Notice {

	/**
	 * Construct function.
	 *
	 * @param string $key key for notice.
	 * @void
	 */
	public function __construct( $key ) {
		parent::__construct( $key );
		$this->type         = 'shop';
		$this->autodestruct = false;
		add_action( 'wp_ajax_validate_shop_code', array( $this, 'validate_shop_code_callback' ) );

	}

	/**
	 * Check if notice is still valid.
	 *
	 * @boolean
	 */
	public function is_valid() {
		return get_transient( 'bw_shop_sha1' ) && get_transient( 'bw_shop_token' );
	}

	/**
	 * Ajax callback. Validate 6 digit shop code.
	 *
	 * @void
	 */
	public function validate_shop_code_callback() {
		check_ajax_referer( 'boxtale_woocommerce', 'security' );
		header( 'Content-Type: application/json; charset=utf-8' );
		if ( ! isset( $_REQUEST['input'] ) ) {
			Notices::add_notice(
				'custom', array(
					'status'  => 'warning',
					'message' => __( 'Your validation code must contain 6 numbers exactly. Please try again.', 'boxtal-woocommerce' ),
				)
			);
			wp_send_json( true );
		}

		$input = sanitize_text_field( wp_unslash( $_REQUEST['input'] ) );
		if ( strlen( $input ) !== 6 ) {
			Notices::add_notice(
				'custom', array(
					'status'  => 'warning',
					'message' => __( 'Your validation code must contain 6 numbers exactly. Please try again.', 'boxtal-woocommerce' ),
				)
			);
			wp_send_json( true );
		}

		$sha1  = get_transient( 'bw_shop_sha1' );
		$token = get_transient( 'bw_shop_token' );
		if ( false === $sha1 || false === $token ) {
			Notices::add_notice(
				'custom', array(
					'status'  => 'warning',
					'message' => __( 'You failed to pair your site in time. Please restart the pairing procedure on Boxtal.', 'boxtal-woocommerce' ),
				)
			);
			wp_send_json( true );
		}

		if ( sha1( $token ) === $sha1 ) {
			Notices::add_notice(
				'custom', array(
					'status'  => 'success',
					'message' => __( 'Congratulations! You\'ve successfully paired your site with Boxtal.', 'boxtal-woocommerce' ),
				)
			);
			Notices::remove_notice( 'shop' );
			update_option( 'BW_API_TOKEN', $token );
		} else {
			Notices::add_notice(
				'custom', array(
					'status'  => 'warning',
					'message' => __( 'Wrong code! Try again.', 'boxtal-woocommerce' ),
				)
			);
		}
		wp_send_json( true );
	}
}
