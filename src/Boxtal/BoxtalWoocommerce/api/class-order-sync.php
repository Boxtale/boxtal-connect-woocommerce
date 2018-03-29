<?php
/**
 * Contains code for the order sync class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Api
 */

namespace Boxtal\BoxtalWoocommerce\Api;

use Boxtal\BoxtalWoocommerce\Helpers\Product_Helper;
use Boxtal\BoxtalWoocommerce\Helpers\Order_Helper;

/**
 * Order sync container class.
 *
 * Opens API endpoint to sync orders.
 *
 * @class       Order_Sync
 * @package     Boxtal\BoxtalWoocommerce\Api
 * @category    Class
 * @author      API Boxtal
 */
class Order_Sync {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'woocommerce_api_boxtal_pull_orders', array( $this, 'api_callback_handler' ) );
	}

	/**
	 * Endpoint callback.
	 *
	 * @void
	 */
	public function api_callback_handler() {
		$response = $this->get_orders();
		echo wp_json_encode( $response );
		die();
	}

	/**
	 * Get Woocommerce orders.
	 *
	 * @return WC_Order[] $result
	 */
	public function get_orders() {
		$result = array();
		foreach ( wc_get_orders( array() ) as $order ) {
			$recipient = array(
				'firstname' => Order_Helper::get_shipping_first_name( $order ),
				'lastname'  => Order_Helper::get_shipping_last_name( $order ),
				'company'   => Order_Helper::get_shipping_company( $order ),
				'address'   => Order_Helper::get_shipping_address_1( $order ) . ' ' . Order_Helper::get_shipping_address_2( $order ),
				'city'      => Order_Helper::get_shipping_city( $order ),
				'state'     => Order_Helper::get_shipping_state( $order ),
				'postcode'  => Order_Helper::get_shipping_postcode( $order ),
				'country'   => Order_Helper::get_shipping_country( $order ),
				'phone'     => Order_Helper::get_billing_phone( $order ),
				'email'     => Order_Helper::get_billing_email( $order ),
			);
			$products  = array();
			foreach ( $order->get_items( 'line_item' ) as $item ) {
				$product                = array();
				$product_id             = 0 !== $item['variation_id'] ? $item['variation_id'] : $item['product_id'];
				$product['weight']      = false !== Product_Helper::get_product_weight( $product_id ) ? (float) Product_Helper::get_product_weight( $product_id ) : null;
				$product['quantity']    = (int) $item['qty'];
				$product['description'] = esc_html( Product_Helper::get_product_description( $item ) );
				$products[]             = $product;
			}
			$result[] = array(
				'recipient' => $recipient,
				'products'  => $products,
			);
		}
		return $result;
	}
}
