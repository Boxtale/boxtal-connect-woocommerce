<?php
/**
 * Contains code for the checkout class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point;

use Boxtal\BoxtalConnectWoocommerce\Util\Order_Util;

/**
 * Checkout class.
 *
 * Handles setter and getter for parcel points.
 *
 * @class       Checkout
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point
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
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'order_created' ), 10, 2 );
	}

	/**
	 * Add parcel point info to order.
	 *
	 * @param string $order_id the order id.
	 * @param array  $posted_data posted data.
	 * @void
	 */
	public function order_created( $order_id, $posted_data ) {
	    // phpcs:ignore
		if ( isset( $posted_data['shipping_method'][0] ) ) {
            // phpcs:ignore
			$carrier  = sanitize_text_field( wp_unslash( $posted_data['shipping_method'][0] ) );
			if ( WC()->session ) {

				$point = Controller::get_chosen_point( $carrier );
				if ( null === $point ) {
					$point = Controller::get_closest_point( $carrier );
				}

				Controller::reset_chosen_points();

				if ( null !== $point ) {
					$order = new \WC_Order( $order_id );
					Order_Util::add_meta_data( $order, 'bw_parcel_point', $point );
					Order_Util::save( $order );
				}
			}
		}
	}
}
