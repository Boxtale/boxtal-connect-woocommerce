<?php
/**
 * Contains code for the configuration class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Rest_Controller
 */

namespace Boxtal\BoxtalWoocommerce\Rest_Controller;

use Boxtal\BoxtalPhp\ApiClient;
use Boxtal\BoxtalPhp\RestClient;
use Boxtal\BoxtalWoocommerce\Notice\Notice_Controller;
use Boxtal\BoxtalWoocommerce\Util\Api_Util;
use Boxtal\BoxtalWoocommerce\Util\Auth_Util;

/**
 * Configuration class.
 *
 * Opens API endpoints to edit configuration.
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
						'methods'             => 'DELETE',
						'callback'            => array( $this, 'delete_configuration_handler' ),
						'permission_callback' => array( $this, 'authenticate' ),
					)
				);
			}
		);

		add_action(
			'rest_api_init', function() {
				register_rest_route(
					'boxtal-woocommerce/v1', '/configuration', array(
						'methods'             => 'PUT',
						'callback'            => array( $this, 'update_configuration_handler' ),
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
	public function delete_configuration_handler( $request ) {
		$body = Auth_Util::decrypt_body( $request->get_body() );

		if ( null === $body ) {
			Api_Util::send_api_response( 400 );
		}

		$this::delete_configuration();
		Api_Util::send_api_response( 200 );
	}

	/**
	 * Endpoint callback.
	 *
	 * @param \WP_REST_Request $request request.
	 * @void
	 */
	public function update_configuration_handler( $request ) {
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
	 * Get configuration.
	 *
	 * @return boolean
	 */
	public static function get_configuration() {
		return self::get_parcel_point_operators() && self::get_map_configuration() && self::get_website_configuration();
	}

	/**
	 * Get parcel point operators.
	 *
	 * @return boolean
	 */
	public static function get_parcel_point_operators() {
		$lib = new ApiClient( null, null );

        //phpcs:disable
        $response = $lib->restClient->request(
            RestClient::$GET,
            $lib->getApiUrl() . '/v2/parcel-point-operator',
            array()
        );
        //phpcs:enable

		if ( ! $response->isError() ) {
			if ( self::parse_parcel_point_operators( $response->response ) ) {
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * Parse parcel point operators response.
	 *
	 * @param object $body body.
	 * @return boolean
	 */
	private static function parse_parcel_point_operators( $body ) {
		if ( is_object( $body ) && property_exists( $body, 'operators' ) ) {

			$stored_operators = get_option( 'BW_PP_OPERATORS' );
			if ( is_array( $stored_operators ) ) {
				$removed_operators = $stored_operators;
                //phpcs:ignore
                foreach ( $body->operators as $new_operator ) {
					foreach ( $stored_operators as $key => $old_operator ) {
						if ( $new_operator->code === $old_operator->code ) {
							unset( $removed_operators[ $key ] );
						}
					}
				}

				if ( count( $removed_operators ) > 0 ) {
					Notice_Controller::add_notice(
						Notice_Controller::$custom, array(
							'status'  => 'warning',
							'message' => __( 'There\'s been a change in Boxtal parcel point operator list, we\'ve adapted your shipping method configuration. Please check that everything is in order.', 'boxtal-woocommerce' ),
						)
					);
				}

                //phpcs:ignore
                $added_operators = $body->operators;
                //phpcs:ignore
                foreach ( $body->operators as $new_operator ) {
					foreach ( $stored_operators as $key => $old_operator ) {
						if ( $new_operator->code === $old_operator->code ) {
							unset( $added_operators[ $key ] );
						}
					}
				}
				if ( count( $added_operators ) > 0 ) {
					Notice_Controller::add_notice(
						Notice_Controller::$custom, array(
							'status'  => 'info',
							'message' => __( 'There\'s been a change in Boxtal parcel point operator list, you can add the extra parcel point operator(s) to your shipping method configuration.', 'boxtal-woocommerce' ),
						)
					);
				}
			}
            //phpcs:ignore
            update_option('BW_PP_OPERATORS', $body->operators);
			return true;
		}
		return false;
	}

	/**
	 * Get map configuration.
	 *
	 * @return boolean
	 */
	public static function get_map_configuration() {
		$lib = new ApiClient( null, null );

        //phpcs:disable
        $response = $lib->restClient->request(
            RestClient::$GET,
            $lib->getApiUrl() . '/v2/maps-configuration',
            array()
        );
        //phpcs:enable

		if ( ! $response->isError() ) {
			if ( self::parse_map_configuration( $response->response ) ) {
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * Parse map configuration.
	 *
	 * @param object $body body.
	 * @return boolean
	 */
	private static function parse_map_configuration( $body ) {
		if ( is_object( $body ) && property_exists( $body, 'bootstrapUrl' ) && property_exists( $body, 'tokenUrl' ) ) {
            //phpcs:ignore
            update_option('BW_MAP_BOOTSTRAP_URL', $body->bootstrapUrl);
            //phpcs:ignore
            update_option('BW_MAP_TOKEN_URL', $body->tokenUrl);
			return true;
		}
		return false;
	}

	/**
	 * Get website configuration.
	 *
	 * @return boolean
	 */
	public static function get_website_configuration() {
		$lib     = new ApiClient( null, null );
		$headers = array(
			'Accept-Language' => get_locale(),
		);
        //phpcs:disable
        $response = $lib->restClient->request(
            RestClient::$GET,
            $lib->getApiUrl() . '/v2/website-configuration',
            array(),
            $headers
        );
        //phpcs:enable

		if ( ! $response->isError() ) {
			if ( self::parse_website_configuration( $response->response ) ) {
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * Parse website configuration.
	 *
	 * @param object $body body.
	 * @return boolean
	 */
	private static function parse_website_configuration( $body ) {
		if ( is_object( $body ) && property_exists( $body, 'accountPageUrl' ) ) {
            //phpcs:ignore
            update_option('BW_ACCOUNT_PAGE_URL', $body->accountPageUrl);
			return true;
		}
		return false;
	}

	/**
	 * Delete configuration.
	 *
	 * @void
	 */
	private static function delete_configuration() {
		global $wpdb;

		delete_option( 'BW_ACCESS_KEY' );
		delete_option( 'BW_SECRET_KEY' );
		delete_option( 'BW_MAP_BOOTSTRAP_URL' );
		delete_option( 'BW_MAP_TOKEN_URL' );
		delete_option( 'BW_ACCOUNT_PAGE_URL' );
		delete_option( 'BW_PP_OPERATORS' );
		delete_option( 'BW_TRACKING_EVENT' );
		delete_option( 'BW_NOTICES' );
		delete_option( 'BW_PAIRING_UPDATE' );
		//phpcs:ignore
		$wpdb->query(
			$wpdb->prepare(
				"
                DELETE FROM $wpdb->options
		        WHERE option_name LIKE %s
		        ",
				'BW_NOTICE_%'
			)
		);
	}
}
