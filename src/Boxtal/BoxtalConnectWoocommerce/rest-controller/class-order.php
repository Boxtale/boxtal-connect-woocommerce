<?php
/**
 * Contains code for the order class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Rest_Controller
 */

namespace Boxtal\BoxtalConnectWoocommerce\Rest_Controller;

use Boxtal\BoxtalConnectWoocommerce\Util\Api_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Order_Item_Shipping_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Product_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Order_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Misc_Util;

/**
 * Order class.
 *
 * Opens API endpoint to sync orders.
 *
 * @class       Order
 * @package     Boxtal\BoxtalConnectWoocommerce\Rest_Controller
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
					'boxtal-connect/v1', '/order', array(
						'methods'             => 'POST',
						'callback'            => array( $this, 'retrieve_orders_handler' ),
						'permission_callback' => array( $this, 'authenticate' ),
					)
				);
			}
		);

		add_action(
			'rest_api_init', function() {
				register_rest_route(
					'boxtal-connect/v1', '/order/(?P<order_id>[\d]+)/tracking', array(
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
		return Auth_Util::authenticate_access_key( $request );
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
		$result           = array();
		$statuses         = Order_Util::get_import_status_list();
		$current_language = get_locale();
		foreach ( wc_get_orders(
			array(
				'status' => array_keys( $statuses ),
				'limit'  => -1,
			)
		) as $order ) {
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
				$product['description'] = array(
					$current_language => esc_html( Product_Util::get_product_description( $item ) ),
				);
				$products[]             = $product;
			}

			$parcel_point         = null;
			$parcel_point_code    = Order_Util::get_meta( $order, 'bw_parcel_point_code' );
			$parcel_point_network = Order_Util::get_meta( $order, 'bw_parcel_point_network' );
			if ( $parcel_point_code && $parcel_point_network ) {
				$parcel_point = array(
					'code'    => $parcel_point_code,
					'network' => $parcel_point_network,
				);
			}

			$status           = Order_Util::get_status( $order );
			$shipping_methods = $order->get_shipping_methods();
			$shipping_method  = ! empty( $shipping_methods ) ? array_shift( $shipping_methods ) : null;
			$result[]         = array(
				'internalReference' => '' . Order_Util::get_id( $order ),
				'reference'         => '' . Order_Util::get_order_number( $order ),
				'status'            => array(
					'key'          => $status,
					'translations' => array(
						$current_language => isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status,
					),
				),
				'shippingMethod'    => array(
					'key'          => Order_Item_Shipping_Util::get_method_id( $shipping_method ),
					'translations' => array(
						$current_language => Order_Item_Shipping_Util::get_name( $shipping_method ),
					),
				),
				'shippingAmount'    => Order_Util::get_shipping_total( $order ),
				'creationDate'      => Order_Util::get_date_created( $order ),
				'orderAmount'       => Order_Util::get_total( $order ),
				'recipient'         => $recipient,
				'products'          => $products,
				'parcelPoint'       => $parcel_point,
			);
		}
		return array( 'orders' => $result );
	}

	/**
	 * Tracking event handler callback.
	 *
	 * @param \WP_REST_Request $request request.
	 * @void
	 */
	public function tracking_event_handler( $request ) {

		if ( ! isset( $request['order_id'] ) ) {
			Api_Util::send_api_response( 400 );
		}

		$body = Auth_Util::decrypt_body( $request->get_body() );

		if ( ! $this::parse_tracking_event( $request['order_id'], $body ) ) {
			Api_Util::send_api_response( 400 );
		}

		Api_Util::send_api_response( 200 );
	}

	/**
	 * Parse tracking event.
	 *
	 * @param int    $order_id order id.
	 * @param object $body request body.
	 * @return boolean
	 */
	public static function parse_tracking_event( $order_id, $body ) {
		if ( ! ( is_object( $body ) && property_exists( $body, 'carrierReference' )
			&& property_exists( $body, 'trackingDate' ) && property_exists( $body, 'trackingCode' ) ) ) {
			return false;
		}

		$tracking_events = get_option( 'BW_TRACKING_EVENTS', array() );
        //phpcs:disable
        $tracking_events[] =  array(
            'order_id'          => $order_id,
            'carrier_reference' => $body->carrierReference,
            'date'              => $body->trackingDate,
            'code'              => $body->trackingCode,
        );
        //phpcs:enable

		update_option( 'BW_TRACKING_EVENTS', $tracking_events );

		return true;
	}
}
