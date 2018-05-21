<?php
/**
 * Order tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Rest_Controller\Order;
use Boxtal\BoxtalWoocommerce\Util\Product_Util;
use Boxtal\BoxtalWoocommerce\Util\Order_Util;

/**
 * Class BW_Test_Order.
 */
class BW_Test_Order extends WP_UnitTestCase {

	/**
	 * Test api callback, simple product.
	 */
	public function test_get_orders_with_simple_product() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );

		$product = WC_Helper_Product::create_simple_product();
		Product_Util::set_weight( $product, 2.5 );
		Product_Util::set_name( $product, 'simple product' );
		Product_Util::save( $product );

		Order_Util::add_product( $order, $product, 4 );
		Order_Util::set_shipping_first_name( $order, 'Jon' );
		Order_Util::set_shipping_last_name( $order, 'Snow' );
		Order_Util::set_shipping_company( $order, 'GoT' );
		Order_Util::set_shipping_address_1( $order, 'House Stark' );
		Order_Util::set_shipping_address_2( $order, 'Winterfell' );
		Order_Util::set_shipping_city( $order, 'Paris' );
		Order_Util::set_shipping_state( $order, '' );
		Order_Util::set_shipping_postcode( $order, '75009' );
		Order_Util::set_shipping_country( $order, 'FR' );
		Order_Util::set_billing_email( $order, 'jsnow@boxtal.com' );
		Order_Util::set_billing_phone( $order, '0612341234' );
		Order_Util::add_meta_data( $order, 'bw_parcel_point_code', 'XXXXXX' );
		Order_Util::add_meta_data( $order, 'bw_parcel_point_operator', 'MONR' );
		Order_Util::save( $order );
		$order->update_status( 'wc-on-hold' );

		$order_rest_controller = new Order();

		$this->assertSame(
			$order_rest_controller->get_orders(), array(
				0 => array(
					'reference'   => '' . Order_Util::get_id( $order ),
					'recipient'   => array(
						'firstname'    => 'Jon',
						'lastname'     => 'Snow',
						'company'      => 'GoT',
						'addressLine1' => 'House Stark',
						'addressLine2' => 'Winterfell',
						'city'         => 'Paris',
						'state'        => null,
						'postcode'     => '75009',
						'country'      => 'FR',
						'phone'        => '0612341234',
						'email'        => 'jsnow@boxtal.com',
					),
					'products'    => array(
						0 => array(
							'weight'      => 2.5,
							'quantity'    => 4,
							'description' => 'simple product',
						),
					),
					'parcelPoint' => array(
						'code'     => 'XXXXXX',
						'operator' => 'MONR',
					),
				),
			)
		);
	}

	/**
	 * Test api callback, variable product.
	 */
	public function test_get_orders_with_variable_product() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );

		$product = WC_Helper_Product::create_variation_product();
		Product_Util::set_weight( $product, 2.5 );
		Product_Util::set_name( $product, 'variation product' );
		Product_Util::save( $product );

		$variations = $product->get_available_variations();
		$variation  = array_shift( $variations );
		Product_Util::set_variation_weight( $variation, 6 );
		$variation_product = Product_Util::get_product( $variation['variation_id'] );

		Order_Util::add_product( $order, $variation_product, 5 );
		Order_Util::set_shipping_first_name( $order, 'Jon' );
		Order_Util::set_shipping_last_name( $order, 'Snow' );
		Order_Util::set_shipping_company( $order, 'GoT' );
		Order_Util::set_shipping_address_1( $order, 'House Stark' );
		Order_Util::set_shipping_address_2( $order, 'Winterfell' );
		Order_Util::set_shipping_city( $order, 'Paris' );
		Order_Util::set_shipping_state( $order, '' );
		Order_Util::set_shipping_postcode( $order, '75009' );
		Order_Util::set_shipping_country( $order, 'FR' );
		Order_Util::set_billing_email( $order, 'jsnow@boxtal.com' );
		Order_Util::set_billing_phone( $order, '0612341234' );
		Order_Util::add_meta_data( $order, 'bw_parcel_point_code', 'XXXXXX' );
		Order_Util::add_meta_data( $order, 'bw_parcel_point_operator', 'MONR' );
		Order_Util::save( $order );
		$order->update_status( 'wc-on-hold' );
		foreach ( $order->get_items( 'line_item' ) as $item ) {
			$product_description = Product_Util::get_product_description( $item );
		}

		$order_rest_controller = new Order();

		$this->assertSame(
			$order_rest_controller->get_orders(), array(
				0 => array(
					'reference'   => '' . Order_Util::get_id( $order ),
					'recipient'   => array(
						'firstname'    => 'Jon',
						'lastname'     => 'Snow',
						'company'      => 'GoT',
						'addressLine1' => 'House Stark',
						'addressLine2' => 'Winterfell',
						'city'         => 'Paris',
						'state'        => null,
						'postcode'     => '75009',
						'country'      => 'FR',
						'phone'        => '0612341234',
						'email'        => 'jsnow@boxtal.com',
					),
					'products'    => array(
						0 => array(
							'weight'      => 6.0,
							'quantity'    => 5,
							'description' => $product_description,
						),
					),
					'parcelPoint' => array(
						'code'     => 'XXXXXX',
						'operator' => 'MONR',
					),
				),
			)
		);
	}
}
