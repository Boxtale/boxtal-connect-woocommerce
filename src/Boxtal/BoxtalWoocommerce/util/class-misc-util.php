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
	 * Is checkout url.
	 *
	 * @return boolean is checkout url
	 */
	public static function is_checkout_url() {
		if ( in_the_loop() ) {
			return (int) get_option( 'woocommerce_checkout_page_id' ) === get_the_ID();
		}
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$checkout_url = self::get_checkout_url();
			$request_uri  = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			return stristr( $checkout_url, $request_uri )
				|| ( false !== strpos( $request_uri, '?' ) && stristr( $checkout_url, substr( $request_uri, 0, strpos( $request_uri, '?' ) ) ) )
				|| ( isset( $_SERVER['HTTP_REFERER'] ) && stristr( $checkout_url, sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) ) && ! stristr( WC()->cart->get_cart_url(), $request_uri ) );
		}
		return false;
	}

	/**
	 * Should display parcel point link.
	 *
	 * @param WC_Shipping_Rate $method woocommmerce shipping rate.
	 * @return boolean should display link
	 */
	public static function should_display_parcel_point_link( $method ) {
		if ( ! self::is_checkout_url() ) {
			return false;
		}

		if ( ! in_array( $method->id, WC()->session->get( 'chosen_shipping_methods' ), true ) ) {
			return false;
		}

		$settings = Shipping_Rate_Util::get_settings( $method );
		if ( ! is_array( $settings ) ) {
			return false;
		}

		if ( ! isset( $settings['bw_map_display'] ) || '0' === $settings['bw_map_display'] ) {
			return false;
		}

		if ( ! isset( $settings['bw_parcel_point_operators'] ) || empty( $settings['bw_parcel_point_operators'] ) ) {
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

		return true;
	}

	/**
	 * Get shipping method settings from method id.
	 *
	 * @param string $method_id woocommerce method id.
	 * @return array $settings method settings
	 */
	public static function get_settings( $method_id ) {
		if ( -1 !== strpos( $method_id, ':' ) ) {
			$method_name  = explode( ':', $method_id );
			$settings_key = 'woocommerce_' . $method_name[0] . '_' . $method_name[1] . '_settings';
		} else {
			$settings_key = 'woocommerce_' . $method_id . '_settings';
		}
		return get_option( $settings_key );
	}
}
