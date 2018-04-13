<?php
/**
 * Contains code for the order sync class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Api
 */

namespace Boxtal\BoxtalWoocommerce\Api;

use Boxtal\BoxtalWoocommerce\Helpers\Auth_Helper;
use Boxtal\BoxtalWoocommerce\Helpers\Product_Helper;
use Boxtal\BoxtalWoocommerce\Helpers\Order_Helper;
use Boxtal\BoxtalWoocommerce\Helpers\Helper_Functions;

/**
 * Order sync class.
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
		add_action(
			'rest_api_init', function() {
				register_rest_route(
					'boxtal-woocommerce/v1', '/order', array(
						'methods'  => 'GET',
						'callback' => array( $this, 'api_callback_handler' ),
						'args'     => array(
							'token' => array(
								'required'          => true,
								'validate_callback' => array( $this, 'authenticate' ),
							),
						),
					)
				);
			}
		);
	}

	/**
	 * Call to auth helper class authenticate function.
	 *
	 * @param string $param param value.
	 * @return WP_Error|boolean
	 */
	public function authenticate( $param ) {
		return Auth_Helper::authenticate( $param );
	}

	/**
	 * Endpoint callback.
	 *
	 * @void
	 */
	public function api_callback_handler() {
		$response = $this->get_orders();
		header( 'Content-Type: application/json; charset=utf-8' );
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
		foreach ( wc_get_orders( array( 'status' => array( 'on-hold', 'processing' ) ) ) as $order ) {
			$recipient = array(
				'firstname'    => Helper_Functions::not_empty_or_null( Order_Helper::get_shipping_first_name( $order ) ),
				'lastname'     => Helper_Functions::not_empty_or_null( Order_Helper::get_shipping_last_name( $order ) ),
				'company'      => Helper_Functions::not_empty_or_null( Order_Helper::get_shipping_company( $order ) ),
				'addressLine1' => Helper_Functions::not_empty_or_null( Order_Helper::get_shipping_address_1( $order ) ),
				'addressLine2' => Helper_Functions::not_empty_or_null( Order_Helper::get_shipping_address_2( $order ) ),
				'city'         => Helper_Functions::not_empty_or_null( Order_Helper::get_shipping_city( $order ) ),
				'state'        => Helper_Functions::not_empty_or_null( Order_Helper::get_shipping_state( $order ) ),
				'postcode'     => Helper_Functions::not_empty_or_null( Order_Helper::get_shipping_postcode( $order ) ),
				'country'      => Helper_Functions::not_empty_or_null( Order_Helper::get_shipping_country( $order ) ),
				'phone'        => Helper_Functions::not_empty_or_null( Order_Helper::get_billing_phone( $order ) ),
				'email'        => Helper_Functions::not_empty_or_null( Order_Helper::get_billing_email( $order ) ),
			);
			$products  = array();
			foreach ( $order->get_items( 'line_item' ) as $item ) {
				$product                = array();
				$variation_id           = $item['variation_id'];
				$product_id             = ( '0' !== $variation_id && 0 !== $variation_id ) ? $variation_id : $item['product_id'];
				$product['weight']      = false !== Product_Helper::get_product_weight( $product_id ) ? (float) Product_Helper::get_product_weight( $product_id ) : null;
				$product['quantity']    = (int) $item['qty'];
				$product['description'] = esc_html( Product_Helper::get_product_description( $item ) );
				$products[]             = $product;
			}
			$result[] = array(
				'reference' => '' . Order_Helper::get_id( $order ),
				'recipient' => $recipient,
				'products'  => $products,
			);
		}
		return $result;
	}
}
