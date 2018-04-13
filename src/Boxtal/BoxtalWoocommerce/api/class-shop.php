<?php
/**
 * Contains code for the shop class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Api
 */

namespace Boxtal\BoxtalWoocommerce\Api;

use Boxtal\BoxtalWoocommerce\Admin\Notices;

/**
 * Shop class.
 *
 * Opens API endpoint to pair.
 *
 * @class       Shop
 * @package     Boxtal\BoxtalWoocommerce\Api
 * @category    Class
 * @author      API Boxtal
 */
class Shop {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action(
			'rest_api_init', function() {
				register_rest_route(
					'boxtal-woocommerce/v1', '/shop', array(
						'methods'  => 'PUT',
						'callback' => array( $this, 'api_callback_handler' ),
					)
				);
			}
		);
	}

	/**
	 * Endpoint callback.
	 *
	 * @void
	 */
	public function api_callback_handler() {
		Notices::add_notice( 'shop' );
        // phpcs:ignore
		if ( isset( $_GET['sha1'] ) ) {
            // phpcs:ignore
			set_transient( 'bw_shop_sha1', sanitize_text_field( wp_unslash( $_GET['sha1'] ) ), 60 * 10 );
		}
        // phpcs:ignore
		if ( isset( $_GET['token'] ) ) {
            // phpcs:ignore
			set_transient( 'bw_shop_token', sanitize_text_field( wp_unslash( $_GET['token'] ) ), 60 * 10 );
		}
		echo 1;
		die();
	}
}
