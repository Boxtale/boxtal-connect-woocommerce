<?php
/**
 * Contains code for misc util class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Util
 */

namespace Boxtal\BoxtalWoocommerce\Util;

/**
 * Misc util class.
 *
 * Miscellaneous util functions.
 *
 * @class       Misc_Util
 * @package     Boxtal\BoxtalWoocommerce\Util
 * @category    Class
 * @author      API Boxtal
 */
class Misc_Util {
	/**
	 * Return value if not empty, null otherwise.
	 *
	 * @param mixed $value value to be checked.
	 * @return mixed $value
	 */
	public static function not_empty_or_null( $value ) {
		return '' === $value ? null : $value;
	}

	/**
	 * Return base64 encoded value if not null.
	 *
	 * @param mixed $value value to be encoded.
	 * @return mixed $value
	 */
	public static function base64_or_null( $value ) {
		return null === $value ? null : base64_encode( $value );
	}

	/**
	 * Get checkout url.
	 *
	 * @return string checkout url
	 */
	public static function get_checkout_url() {
		static $checkout_url;

		if ( null !== $checkout_url ) {
			return $checkout_url;
		}

		if ( function_exists( 'wc_get_checkout_url' ) ) {
			$checkout_url = wc_get_checkout_url();
		} else {
			$checkout_url = WC()->cart->get_checkout_url();
		}
		return $checkout_url;
	}

	/**
	 * Is checkout page.
	 *
	 * @return boolean is checkout page
	 */
	public static function is_checkout_page() {
		if ( in_the_loop() ) {
			return (int) get_option( 'woocommerce_checkout_page_id' ) === get_the_ID();
		}

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$checkout_url = self::get_checkout_url();
			$request_uri  = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );

			if ( self::remove_query_string( $checkout_url ) === self::remove_query_string( $request_uri ) ) {
				return true;
			}

			if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
				$http_referer = sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) );
				if ( false !== strpos( $checkout_url, $http_referer ) && false === strpos( WC()->cart->get_cart_url(), $request_uri ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Helper function to remove query string in url.
	 *
	 * @param string $url url.
	 * @return boolean url without query string
	 */
	public static function remove_query_string( $url ) {
		if ( strpos( $url, '?' ) !== false ) {
			$url = substr( $url, 0, strpos( $url, '?' ) );
		}
		return $url;
	}

	/**
	 * Should display parcel point link.
	 *
	 * @param \WC_Shipping_Rate $method woocommmerce shipping rate.
	 * @return boolean should display link
	 */
	public static function should_display_parcel_point_link( $method ) {

		if ( ! self::is_checkout_page() ) {
			return false;
		}

		if ( ! in_array( $method->id, WC()->session->get( 'chosen_shipping_methods' ), true ) ) {
			return false;
		}

		if ( ! WC()->customer->get_shipping_country() || ! WC()->customer->get_shipping_city() ) {
			return false;
		}

		$countries      = Country_Util::get_activated_countries();
		$address_fields = $countries->get_address_fields( WC()->customer->get_shipping_country(), 'shipping_' );
		if ( $address_fields['shipping_state']['required'] && ! WC()->customer->get_shipping_state() ) {
			return false;
		}

		if ( $address_fields['shipping_postcode']['required'] && ! WC()->customer->get_shipping_postcode() ) {
			return false;
		}

		if ( null === Auth_Util::get_maps_token() ) {
			return false;
		}

		return true;
	}

	/**
	 * Get shipping method settings from method id.
	 *
	 * @param string $method_id woocommerce method id.
	 * @return array $settings method settings
	 */
	public static function get_settings( $method_id ) {
		if ( false !== strpos( $method_id, ':' ) ) {
			$method_name  = explode( ':', $method_id );
			$settings_key = 'woocommerce_' . $method_name[0] . '_' . $method_name[1] . '_settings';
		} else {
			$settings_key = 'woocommerce_' . $method_id . '_settings';
		}
		return get_option( $settings_key );
	}

	/**
	 * Get active parcel point operators for shipping method.
	 *
	 * @param array $settings shipping rate settings.
	 * @return array $operators
	 */
	public static function get_active_parcel_point_operators( $settings ) {
		if ( null === $settings['bw_parcel_point_operators'] || ! is_array( $settings['bw_parcel_point_operators'] ) || empty( $settings['bw_parcel_point_operators'] ) ) {
            return array();
        }
		$operators = get_option( 'BW_PP_OPERATORS' );
		if ( false === $operators || ! is_array( $operators ) ) {
			return array();
		}
		$operators_array = array();
		foreach ($operators as $operator) {
		    $operators_array[] = $operator->code;
        }
		return array_intersect( $operators_array, $settings['bw_parcel_point_operators'] );
	}
}
