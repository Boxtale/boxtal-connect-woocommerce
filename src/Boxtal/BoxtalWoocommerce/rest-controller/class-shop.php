<?php
/**
 * Contains code for the shop class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Rest_Controller
 */

namespace Boxtal\BoxtalWoocommerce\Rest_Controller;

use Boxtal\BoxtalWoocommerce\Notice\Notice_Controller;
use Boxtal\BoxtalWoocommerce\Util\Auth_Util;

/**
 * Shop class.
 *
 * Opens API endpoint to pair.
 *
 * @class       Shop
 * @package     Boxtal\BoxtalWoocommerce\Rest_Controller
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
						'methods'             => 'PUT',
						'callback'            => array( $this, 'api_callback_handler' ),
						'permission_callback' => array( $this, 'authenticate' ),
					)
				);
			}
		);
	}

	/**
	 * Call to auth helper class authenticate function.
	 *
	 * @param WP_REST_Request $request request.
	 * @return WP_Error|boolean
	 */
	public function authenticate( $request ) {
		return Auth_Util::authenticate( $request );
	}

	/**
	 * Endpoint callback.
	 *
	 * @void
	 */
	public function api_callback_handler() {
		Notice_Controller::remove_notice( 'setup-wizard' );
		update_option( 'BW_PLUGIN_SETUP', true );
		Notice_Controller::add_notice(
			'pairing', array(
				'status'  => 'success',
				'message' => __( 'Congratulations! You\'ve successfully paired your site with Boxtal.', 'boxtal-woocommerce' ),
			)
		);
		echo 1;
		die();
	}
}
