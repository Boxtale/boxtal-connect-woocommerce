<?php
/**
 * Contains code for the checkout class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point;

use Boxtal\BoxtalWoocommerce\Util\Misc_Util;
use Boxtal\BoxtalWoocommerce\Util\Order_Util;

/**
 * Checkout class.
 *
 * Handles setter and getter for parcel points.
 *
 * @class       Checkout
 * @package     Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point
 * @category    Class
 * @author      API Boxtal
 */
class Checkout {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'woocommerce_checkout_process', array( $this, 'validate_checkout' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'order_created' ) );
	}

	/**
	 * Validate checkout.
	 *
	 * @void
	 */
	public function validate_checkout() {
        // phpcs:ignore
		if ( isset( $_REQUEST['shipping_method'][0] ) ) {
            // phpcs:ignore
			$carrier  = sanitize_text_field( wp_unslash( $_REQUEST['shipping_method'][0] ) );
			$settings = Misc_Util::get_settings( $carrier );

			if ( ! isset( $settings['bw_parcel_point_operators'] ) || empty( $settings['bw_parcel_point_operators'] ) ) {
				return;
			}

			if ( WC()->session ) {
				$session_map_url = WC()->session->get( 'bw_map_url', false );
				if ( ! $session_map_url ) {
					return;
				}

				$session_parcel_point_code     = WC()->session->get( 'bw_parcel_point_code_' . $carrier, false );
				$session_parcel_point_operator = WC()->session->get( 'bw_parcel_point_operator_' . $carrier, false );
				if ( ! $session_parcel_point_code || ! $session_parcel_point_operator ) {
					wc_add_notice( __( 'Please select a parcel point', 'boxtal-woocommerce' ), 'error' );
				}
			} else {
				wc_add_notice( __( 'Could not set parcel point. Woocommerce sessions are not enabled!', 'boxtal-woocommerce' ), 'error' );
			}
		}
	}

	/**
	 * Add parcel point info to order.
	 *
	 * @param string $order_id the order id.
	 * @void
	 */
	public function order_created( $order_id ) {
	    // phpcs:ignore
		if ( isset( $_REQUEST['shipping_method'][0] ) ) {
            // phpcs:ignore
			$carrier  = sanitize_text_field( wp_unslash( $_REQUEST['shipping_method'][0] ) );
			if ( WC()->session ) {
				$session_parcel_point_code     = WC()->session->get( 'bw_parcel_point_code_' . $carrier, false );
				$session_parcel_point_operator = WC()->session->get( 'bw_parcel_point_operator_' . $carrier, false );
				if ( $session_parcel_point_code && $session_parcel_point_operator ) {
					$order = new \WC_Order( $order_id );
					Order_Util::add_meta_data( $order, 'bw_parcel_point_code', $session_parcel_point_code );
					Order_Util::add_meta_data( $order, 'bw_parcel_point_operator', $session_parcel_point_operator );
					Order_Util::save( $order );
				}
			}
		}
	}
}
