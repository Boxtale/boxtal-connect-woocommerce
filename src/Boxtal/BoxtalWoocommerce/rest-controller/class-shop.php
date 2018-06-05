<?php
/**
 * Contains code for the shop class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Rest_Controller
 */

namespace Boxtal\BoxtalWoocommerce\Rest_Controller;

use Boxtal\BoxtalWoocommerce\Notice\Notice_Controller;
use Boxtal\BoxtalWoocommerce\Util\Api_Util;
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
						'methods'             => 'PATCH',
						'callback'            => array( $this, 'pairing_handler' ),
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
	 * @param WP_REST_Request $request request.
	 * @void
	 */
	public function pairing_handler( $request ) {
		$body = Auth_Util::decrypt_body( $request->get_body() );

		if ( null === $body ) {
			Api_Util::send_api_response( 400 );
		}

		$access_key   = null;
		$secret_key   = null;
		$callback_url = null;
		if ( is_object( $body ) && property_exists( $body, 'accessKey' ) && property_exists( $body, 'secretKey' ) ) {
			//phpcs:ignore
		    $access_key = $body->accessKey;
            //phpcs:ignore
			$secret_key = $body->secretKey;

			if ( property_exists( $body, 'pairCallbackUrl' ) ) {
                //phpcs:ignore
                $callback_url = $body->pairCallbackUrl;
			}
		}

		if ( null !== $access_key && null !== $secret_key ) {
			if ( ! Auth_Util::is_plugin_paired() ) { // initial pairing.
				Auth_Util::pair_plugin( $access_key, $secret_key );
				Notice_Controller::remove_notice( 'setup-wizard' );
				Notice_Controller::add_notice( 'pairing', array( 'result' => 1 ) );
				Api_Util::send_api_response( 200 );
			} else { // pairing update.
				if ( null !== $callback_url ) {
					Auth_Util::pair_plugin( $access_key, $secret_key );
					Notice_Controller::remove_notice( 'pairing' );
					Auth_Util::start_pairing_update( $callback_url );
					Notice_Controller::add_notice( 'pairing-update' );
					Api_Util::send_api_response( 200 );
				} else {
					Api_Util::send_api_response( 403 );
				}
			}
		} else {
			Notice_Controller::add_notice( 'pairing', array( 'result' => 0 ) );
			Api_Util::send_api_response( 400 );
		}
	}
}
