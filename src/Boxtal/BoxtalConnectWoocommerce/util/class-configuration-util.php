<?php
/**
 * Contains code for the configuration util class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Util
 */

namespace Boxtal\BoxtalConnectWoocommerce\Util;

use Boxtal\BoxtalConnectWoocommerce\Notice\Notice_Controller;
use Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point\Controller;

/**
 * Configuration util class.
 *
 * Helper to manage configuration.
 *
 * @class       Configuration_Util
 * @package     Boxtal\BoxtalConnectWoocommerce\Util
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
		$params = array(
			'acceptLanguage' => get_locale(),
			'email'          => get_option( 'admin_email' ),
			'shopUrl'        => get_option( 'siteurl' ),
			'shopType'       => 'woocommerce',
		);
		return $url . '?' . http_build_query( $params );
	}

	/**
	 * Get help center url
	 *
	 * @return string onboarding link
	 */
	public static function get_help_center_link() {
		$url = get_option( 'BW_HELP_CENTER_URL' );
		return false !== $url ? $url : null;
	}

	/**
	 * Get map logo href url.
	 *
	 * @return string map logo href url
	 */
	public static function get_map_logo_href_url() {
		$url = get_option( 'BW_MAP_LOGO_HREF_URL' );
		return false !== $url ? $url : null;
	}

	/**
	 * Get map logo image url.
	 *
	 * @return string map logo image url
	 */
	public static function get_map_logo_image_url() {
		$url = get_option( 'BW_MAP_LOGO_IMAGE_URL' );
		return false !== $url ? $url : null;
	}

	/**
	 * Has configuration.
	 *
	 * @return boolean
	 */
	public static function has_configuration() {
		return false !== get_option( 'BW_MAP_BOOTSTRAP_URL' ) && false !== get_option( 'BW_MAP_TOKEN_URL' ) && false !== Controller::get_network_list();
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
		delete_option( 'BW_MAP_LOGO_IMAGE_URL' );
		delete_option( 'BW_MAP_LOGO_HREF_URL' );
		delete_option( 'BW_PP_NETWORKS' );
		delete_option( 'BW_TRACKING_EVENTS' );
		delete_option( 'BW_NOTICES' );
		delete_option( 'BW_PAIRING_UPDATE' );
		delete_option( 'BW_ORDER_SHIPPED' );
		delete_option( 'BW_ORDER_DELIVERED' );
		delete_option( 'BW_HELP_CENTER_URL' );
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
		return self::parse_parcel_point_networks( $body )
			&& self::parse_map_configuration( $body )
			&& self::parse_help_center_configuration( $body );
	}

	/**
	 * Is first activation.
	 *
	 * @return boolean
	 */
	public static function is_first_activation() {
		return false === get_option( 'BW_NOTICES' );
	}

	/**
	 * Parse parcel point networks response.
	 *
	 * @param object $body body.
	 * @return boolean
	 */
	private static function parse_parcel_point_networks( $body ) {
		if ( is_object( $body ) && property_exists( $body, 'parcelPointNetworks' ) ) {

			$stored_networks = Controller::get_network_list();
			if ( is_array( $stored_networks ) ) {
				$removed_networks = $stored_networks;
                //phpcs:ignore
                foreach ( $body->parcelPointNetworks as $new_network => $new_network_carriers ) {
					foreach ( $stored_networks as $old_network => $old_network_carriers ) {
						if ( $new_network === $old_network ) {
							unset( $removed_networks[ $old_network ] );
						}
					}
				}

				if ( count( $removed_networks ) > 0 ) {
					Notice_Controller::add_notice(
						Notice_Controller::$custom, array(
							'status'  => 'warning',
							'message' => __( 'There\'s been a change in the parcel point network list, we\'ve adapted your shipping method configuration. Please check that everything is in order.', 'boxtal-connect' ),
						)
					);
				}

                //phpcs:ignore
                $added_networks = $body->parcelPointNetworks;
                //phpcs:ignore
                foreach ( $body->parcelPointNetworks as $new_network => $new_network_carriers ) {
					foreach ( $stored_networks as $old_network => $old_network_carriers ) {
						if ( $new_network === $old_network ) {
							unset( $added_networks[ $old_network ] );
						}
					}
				}
				if ( count( $added_networks ) > 0 ) {
					Notice_Controller::add_notice(
						Notice_Controller::$custom, array(
							'status'  => 'info',
							'message' => __( 'There\'s been a change in the parcel point network list, you can add the extra parcel point network(s) to your shipping method configuration.', 'boxtal-connect' ),
						)
					);
				}
			}
            //phpcs:ignore
            update_option('BW_PP_NETWORKS', $body->parcelPointNetworks);
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
		if ( is_object( $body ) && property_exists( $body, 'mapsBootstrapUrl' ) && property_exists( $body, 'mapsTokenUrl' )
			&& property_exists( $body, 'mapsLogoImageUrl' ) && property_exists( $body, 'mapsLogoHrefUrl' ) ) {
            //phpcs:ignore
            update_option('BW_MAP_BOOTSTRAP_URL', $body->mapsBootstrapUrl);
            //phpcs:ignore
            update_option('BW_MAP_TOKEN_URL', $body->mapsTokenUrl);
            //phpcs:ignore
            update_option('BW_MAP_LOGO_IMAGE_URL', $body->mapsLogoImageUrl);
            //phpcs:ignore
            update_option('BW_MAP_LOGO_HREF_URL', $body->mapsLogoHrefUrl);
			return true;
		}
		return false;
	}

	/**
	 * Parse help center configuration.
	 *
	 * @param object $body body.
	 * @return boolean
	 */
	private static function parse_help_center_configuration( $body ) {
		if ( is_object( $body ) && property_exists( $body, 'helpCenterUrl' ) ) {
            //phpcs:ignore
            update_option('BW_HELP_CENTER_URL', $body->helpCenterUrl);
			return true;
		}
		return false;
	}
}
