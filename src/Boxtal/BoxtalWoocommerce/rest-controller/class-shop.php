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
use Boxtal\BoxtalWoocommerce\Util\Configuration_Util;

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
					'boxtal-woocommerce/v1', '/shop/pair', array(
						'methods'             => 'PATCH',
						'callback'            => array( $this, 'pairing_handler' ),
						'permission_callback' => array( $this, 'authenticate' ),
					)
				);
			}
		);

        add_action(
            'rest_api_init', function() {
                register_rest_route(
                    'boxtal-woocommerce/v1', '/shop/configuration', array(
                        'methods'             => 'PATCH',
                        'callback'            => array( $this, 'update_configuration_handler' ),
                        'permission_callback' => array( $this, 'authenticate' ),
                    )
                );
            }
        );


        add_action(
            'rest_api_init', function() {
                register_rest_route(
                    'boxtal-woocommerce/v1', '/shop/configuration', array(
                        'methods'             => 'DELETE',
                        'callback'            => array( $this, 'delete_configuration_handler' ),
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
				Notice_Controller::remove_notice( Notice_Controller::$setup_wizard );
				Notice_Controller::add_notice( Notice_Controller::$pairing, array( 'result' => 1 ) );
				Api_Util::send_api_response( 200 );
			} else { // pairing update.
				if ( null !== $callback_url ) {
					Auth_Util::pair_plugin( $access_key, $secret_key );
					Notice_Controller::remove_notice( Notice_Controller::$pairing );
					Auth_Util::start_pairing_update( $callback_url );
					Notice_Controller::add_notice( Notice_Controller::$pairing_update );
					Api_Util::send_api_response( 200 );
				} else {
					Api_Util::send_api_response( 403 );
				}
			}
		} else {
			Notice_Controller::add_notice( Notice_Controller::$pairing, array( 'result' => 0 ) );
			Api_Util::send_api_response( 400 );
		}
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

        Configuration_Util::delete_configuration();
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
     * Parse configuration.
     *
     * @param object $body body.
     * @return boolean
     */
    public static function parse_configuration( $body ) {
        return self::parse_parcel_point_operators( $body ) && self::parse_map_configuration( $body );
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
}
