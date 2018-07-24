<?php
/**
 * Contains code for the order class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Rest_Controller
 */

namespace Boxtal\BoxtalWoocommerce\Rest_Controller;

use Boxtal\BoxtalWoocommerce\Util\Api_Util;
use Boxtal\BoxtalWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalWoocommerce\Util\Product_Util;
use Boxtal\BoxtalWoocommerce\Util\Order_Util;
use Boxtal\BoxtalWoocommerce\Util\Misc_Util;

/**
 * Order class.
 *
 * Opens API endpoint to sync orders.
 *
 * @class       Order
 * @package     Boxtal\BoxtalWoocommerce\Rest_Controller
 * @category    Class
 * @author      API Boxtal
 */
class Order {

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
						'methods'             => 'PATCH',
						'callback'            => array( $this, 'retrieve_orders_handler' ),
						'permission_callback' => array( $this, 'authenticate' ),
					)
				);
			}
		);

		add_action(
			'rest_api_init', function() {
				register_rest_route(
					'boxtal-woocommerce/v1', '/order/(?P<order_id>[*]+)', array(
						'methods'             => 'PATCH',
						'callback'            => array( $this, 'update_order_handler' ),
						'permission_callback' => array( $this, 'authenticate' ),
					)
				);
			}
		);

		add_action(
			'rest_api_init', function() {
				register_rest_route(
					'boxtal-woocommerce/v1', '/order/(?P<order_id>[*]+)/tracking', array(
						'methods'             => 'POST',
						'callback'            => array( $this, 'tracking_event_handler' ),
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
	 * Retrieve orders callback.
	 *
	 * @void
	 */
	public function retrieve_orders_handler() {
		$response = $this->get_orders();
		Api_Util::send_api_response( 200, $response );
	}

	/**
	 * Get Woocommerce orders.
	 *
	 * @return array $result
	 */
	public function get_orders() {
		$result   = array();
		$statuses = Order_Util::get_import_status_list();
		foreach ( wc_get_orders( array( 'status' => $statuses ) ) as $order ) {
			$recipient = array(
				'firstname'    => Misc_Util::not_empty_or_null( Order_Util::get_shipping_first_name( $order ) ),
				'lastname'     => Misc_Util::not_empty_or_null( Order_Util::get_shipping_last_name( $order ) ),
				'company'      => Misc_Util::not_empty_or_null( Order_Util::get_shipping_company( $order ) ),
				'addressLine1' => Misc_Util::not_empty_or_null( Order_Util::get_shipping_address_1( $order ) ),
				'addressLine2' => Misc_Util::not_empty_or_null( Order_Util::get_shipping_address_2( $order ) ),
				'city'         => Misc_Util::not_empty_or_null( Order_Util::get_shipping_city( $order ) ),
				'state'        => Misc_Util::not_empty_or_null( Order_Util::get_shipping_state( $order ) ),
				'postcode'     => Misc_Util::not_empty_or_null( Order_Util::get_shipping_postcode( $order ) ),
				'country'      => Misc_Util::not_empty_or_null( Order_Util::get_shipping_country( $order ) ),
				'phone'        => Misc_Util::not_empty_or_null( Order_Util::get_billing_phone( $order ) ),
				'email'        => Misc_Util::not_empty_or_null( Order_Util::get_billing_email( $order ) ),
			);
			$products  = array();
			foreach ( $order->get_items( 'line_item' ) as $item ) {
				$product                = array();
				$variation_id           = $item['variation_id'];
				$product_id             = ( '0' !== $variation_id && 0 !== $variation_id ) ? $variation_id : $item['product_id'];
				$product['weight']      = false !== Product_Util::get_product_weight( $product_id ) ? (float) Product_Util::get_product_weight( $product_id ) : null;
				$product['quantity']    = (int) $item['qty'];
				$product['price']       = Product_Util::get_product_price( $product_id );
				$product['description'] = esc_html( Product_Util::get_product_description( $item ) );
				$products[]             = $product;
			}

			$parcel_point          = null;
			$parcel_point_code     = Order_Util::get_meta( $order, 'bw_parcel_point_code' );
			$parcel_point_operator = Order_Util::get_meta( $order, 'bw_parcel_point_operator' );
			if ( $parcel_point_code && $parcel_point_operator ) {
				$parcel_point = array(
					'code'     => $parcel_point_code,
					'operator' => $parcel_point_operator,
				);
			}

			$result[] = array(
				'reference'      => '' . Order_Util::get_id( $order ),
				'status'         => Order_Util::get_status( $order ),
				'shippingMethod' => Misc_Util::not_empty_or_null( $order->get_shipping_method() ),
				'shippingAmount' => $order->get_shipping_total(),
				'creationDate'   => $order->get_date_created()->date( 'Y-m-d H:i:s' ),
				'orderAmount'    => $order->get_total(),
				'recipient'      => $recipient,
				'products'       => $products,
				'parcelPoint'    => $parcel_point,
			);
		}
		return $result;
	}

	/**
	 * Update order callback.
	 *
	 * @param WP_REST_Request $request request.
	 * @void
	 */
	public function update_order_handler( $request ) {

		$body = Auth_Util::decrypt_body( $request->get_body() );

		if ( ! is_array( $body ) || ! isset( $request['order_id'] ) ) {
			Api_Util::send_api_response( 400 );
		}

		if ( count( $body ) > 0 ) {
			update_post_meta( $request['order_id'], 'bw_shipments', $body );
		}

		Api_Util::send_api_response( 200 );
	}

	/**
	 * Tracking event handler callback.
	 *
	 * @param WP_REST_Request $request request.
	 * @void
	 */
	public function tracking_event_handler( $request ) {

		if ( ! isset( $request['order_id'] ) ) {
			Api_Util::send_api_response( 400 );
		}

		$body = Auth_Util::decrypt_body( $request->get_body() );
		if ( ! ( is_object( $body ) && property_exists( $body, 'carrierReference' )
			&& property_exists( $body, 'trackingDate' ) && property_exists( $body, 'trackingCode' ) ) ) {
			Api_Util::send_api_response( 400 );
		}

		//phpcs:disable
		update_option(
			'BW_TRACKING_EVENT', array(
				'order_id'          => $request['order_id'],
				'carrier_reference' => $body->carrierReference,
				'date'              => $body->trackingDate,
				'code'              => $body->trackingCode,
			)
		);
        //phpcs:enable

		Api_Util::send_api_response( 200 );
	}
}
