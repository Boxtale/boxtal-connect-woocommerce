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
	 * @param WP_REST_Request $request request.
	 * @void
	 */
	public function api_callback_handler( $request ) {
		$params = $request->get_json_params();
        // phpcs:ignore
		if ( isset( $params['callbackUrl'] ) ) {
            // phpcs:ignore
			set_transient( 'bw_callback_url', sanitize_text_field( wp_unslash( $params['callbackUrl'] ) ), 60 * 10 );
		}
		Notices::remove_notice( 'setup-wizard' );
		update_option( 'BW_PLUGIN_SETUP', true );
		Notices::add_notice( 'shop' );
		echo 1;
		die();
	}
}
