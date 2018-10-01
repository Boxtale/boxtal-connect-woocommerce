<?php
/**
 * Contains code for the configuration util class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Util
 */

namespace Boxtal\BoxtalWoocommerce\Util;

use Boxtal\BoxtalWoocommerce\Notice\Notice_Controller;

/**
 * Configuration util class.
 *
 * Helper to manage configuration.
 *
 * @class       Configuration_Util
 * @package     Boxtal\BoxtalWoocommerce\Util
 * @category    Class
 * @author      API Boxtal
 */
class Configuration_Util {

	/**
	 * Build onboarding link.
	 *
	 * @return string onboarding link
	 */
	public static function get_onboarding_link() {
		$url    = BW_ONBOARDING_URL;
		$admins = get_super_admins();
		if ( is_array( $admins ) && count( $admins ) > 0 ) {
			$admin_user_login = array_shift( $admins );
			$admin_user       = get_user_by( 'login', $admin_user_login );
			$admin_user_id    = $admin_user->get( 'ID' );
		} else {
			$admin_user_id = 1;
		}

		$customer = new \WC_Customer( $admin_user_id );
		$params   = array(
			'acceptLanguage' => get_locale(),
			'email'          => Customer_Util::get_email( $customer ),
			'shopUrl'        => get_option( 'siteurl' ),
			'shopType'       => 'woocommerce',
		);
		return $url . '?' . http_build_query( $params );
	}

	/**
	 * Has configuration.
	 *
	 * @return boolean
	 */
	public static function has_configuration() {
		return false !== get_option( 'BW_MAP_BOOTSTRAP_URL' ) && false !== get_option( 'BW_MAP_TOKEN_URL' ) && false !== get_option( 'BW_PP_OPERATORS' );
	}

	/**
	 * Delete configuration.
	 *
	 * @void
	 */
	public static function delete_configuration() {
		global $wpdb;

		delete_option( 'BW_ACCESS_KEY' );
		delete_option( 'BW_SECRET_KEY' );
		delete_option( 'BW_MAP_BOOTSTRAP_URL' );
		delete_option( 'BW_MAP_TOKEN_URL' );
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
        if ( is_object( $body ) && property_exists( $body, 'parcelPointOperators' ) ) {

            $stored_operators = get_option( 'BW_PP_OPERATORS' );
            if ( is_array( $stored_operators ) ) {
                $removed_operators = $stored_operators;
                //phpcs:ignore
                foreach ( $body->parcelPointOperators as $new_operator ) {
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
                            'message' => __( 'There\'s been a change in Boxtal\'s parcel point operator list, we\'ve adapted your shipping method configuration. Please check that everything is in order.', 'boxtal-woocommerce' ),
                        )
                    );
                }

                //phpcs:ignore
                $added_operators = $body->parcelPointOperators;
                //phpcs:ignore
                foreach ( $body->parcelPointOperators as $new_operator ) {
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
                            'message' => __( 'There\'s been a change in Boxtal\'s parcel point operator list, you can add the extra parcel point operator(s) to your shipping method configuration.', 'boxtal-woocommerce' ),
                        )
                    );
                }
            }
            //phpcs:ignore
            update_option('BW_PP_OPERATORS', $body->parcelPointOperators);
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
        if ( is_object( $body ) && property_exists( $body, 'mapsBootstrapUrl' ) && property_exists( $body, 'mapsTokenUrl' ) ) {
            //phpcs:ignore
            update_option('BW_MAP_BOOTSTRAP_URL', $body->mapsBootstrapUrl);
            //phpcs:ignore
            update_option('BW_MAP_TOKEN_URL', $body->mapsTokenUrl);
            return true;
        }
        return false;
    }
}
