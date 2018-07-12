<?php
/**
 * Contains code for the configuration class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Rest_Controller
 */

namespace Boxtal\BoxtalWoocommerce\Rest_Controller;

use Boxtal\BoxtalPhp\ApiClient;
use Boxtal\BoxtalPhp\RestClient;
use Boxtal\BoxtalWoocommerce\Util\Api_Util;
use Boxtal\BoxtalWoocommerce\Util\Auth_Util;

/**
 * Configuration class.
 *
 * Opens API endpoint to modify configuration.
 *
 * @class       Configuration
 * @package     Boxtal\BoxtalWoocommerce\Rest_Controller
 * @category    Class
 * @author      API Boxtal
 */
class Configuration {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action(
			'rest_api_init', function() {
				register_rest_route(
					'boxtal-woocommerce/v1', '/configuration', array(
						'methods'             => 'PUT',
						'callback'            => array( $this, 'configuration_handler' ),
						'permission_callback' => array( $this, 'authenticate' ),
					)
				);
			}
		);
	}

	/**
	 * Call to auth helper class authenticate function.
	 *
	 * @param \WP_REST_Request $request request.
	 * @return \WP_Error|boolean
	 */
	public function authenticate( $request ) {
		return Auth_Util::authenticate( $request );
	}

	/**
	 * Endpoint callback.
	 *
	 * @param \WP_REST_Request $request request.
	 * @void
	 */
	public function configuration_handler( $request ) {
		$body = Auth_Util::decrypt_body( $request->get_body() );

		if ( null === $body ) {
			Api_Util::send_api_response( 400 );
		}

		if ( $this::parse_configuration( $body ) ) {
			Api_Util::send_api_response( 200 );
		}

		Api_Util::send_api_response( 400 );
	}

	/**
	 * Parse configuration.
	 *
	 * @param object $body body.
	 * @return boolean
	 */
	private static function parse_configuration( $body ) {
		if ( is_object( $body ) && property_exists( $body, 'mapsEndpointUrl' ) && property_exists( $body, 'mapsTokenUrl' )
			&& property_exists( $body, 'signupPageUrl' ) && property_exists( $body, 'parcelPointOperators' ) ) {
            //phpcs:ignore
            update_option('BW_MAP_URL', $body->mapsEndpointUrl);
            //phpcs:ignore
            update_option('BW_TOKEN_URL', $body->mapsTokenUrl);
            //phpcs:ignore
            update_option('BW_SIGNUP_URL', $body->signupPageUrl);
            //phpcs:ignore
            update_option('BW_PP_OPERATORS', $body->parcelPointOperators);
			return true;
		}
		return false;
	}

	/**
	 * Get configuration.
	 *
	 * @return boolean
	 */
	public static function get_configuration() {
		$lib    = new ApiClient( null, null );
		$params = array(
			'locale' => get_locale(),
		);
        //phpcs:disable
        $response = $lib->restClient->request(
            RestClient::$GET,
            $lib->getApiUrl() . '/v2/sellershop/module/config',
            $params
        );

        if ( ! $response->isError() ) {
            if (self::parse_configuration($response->response)) {
                return true;
            }
            return false;
        }
        return false;
    }
}
