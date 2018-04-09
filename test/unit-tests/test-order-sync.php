<?php
/**
 * Order sync tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Api\Order_Sync;
use Boxtal\BoxtalWoocommerce\Helpers\Product_Helper;
use Boxtal\BoxtalWoocommerce\Helpers\Order_Helper;

/**
 * Class BW_Test_Order_Sync.
 */
class BW_Test_Order_Sync extends WC_Unit_Test_Case {


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
		Product_Helper::set_weight( $product, 2.5 );
		Product_Helper::set_name( $product, 'simple product' );
		Product_Helper::save( $product );

		Order_Helper::add_product( $order, $product, 4 );
		Order_Helper::set_shipping_first_name( $order, 'Jon' );
		Order_Helper::set_shipping_last_name( $order, 'Snow' );
		Order_Helper::set_shipping_company( $order, 'GoT' );
		Order_Helper::set_shipping_address_1( $order, 'House Stark' );
		Order_Helper::set_shipping_address_2( $order, 'Winterfell' );
		Order_Helper::set_shipping_city( $order, 'Paris' );
		Order_Helper::set_shipping_state( $order, '' );
		Order_Helper::set_shipping_postcode( $order, '75009' );
		Order_Helper::set_shipping_country( $order, 'FR' );
		Order_Helper::set_billing_email( $order, 'jsnow@boxtal.com' );
		Order_Helper::set_billing_phone( $order, '0612341234' );
		Order_Helper::save( $order );
		$order->update_status( 'wc-on-hold' );

		$order_sync = new Order_Sync();

		$this->assertSame(
			$order_sync->get_orders(), array(
				0 => array(
					'reference' => '' . Order_Helper::get_id( $order ),
					'recipient' => array(
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
					'products'  => array(
						0 => array(
							'weight'      => 2.5,
							'quantity'    => 4,
							'description' => 'simple product',
						),
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
		Product_Helper::set_weight( $product, 2.5 );
		Product_Helper::set_name( $product, 'variation product' );
		Product_Helper::save( $product );

		$variations = $product->get_available_variations();
		$variation  = array_shift( $variations );
		Product_Helper::set_variation_weight( $variation, 6 );
		$variation_product = Product_Helper::get_product( $variation['variation_id'] );

		Order_Helper::add_product( $order, $variation_product, 5 );
		Order_Helper::set_shipping_first_name( $order, 'Jon' );
		Order_Helper::set_shipping_last_name( $order, 'Snow' );
		Order_Helper::set_shipping_company( $order, 'GoT' );
		Order_Helper::set_shipping_address_1( $order, 'House Stark' );
		Order_Helper::set_shipping_address_2( $order, 'Winterfell' );
		Order_Helper::set_shipping_city( $order, 'Paris' );
		Order_Helper::set_shipping_state( $order, '' );
		Order_Helper::set_shipping_postcode( $order, '75009' );
		Order_Helper::set_shipping_country( $order, 'FR' );
		Order_Helper::set_billing_email( $order, 'jsnow@boxtal.com' );
		Order_Helper::set_billing_phone( $order, '0612341234' );
		Order_Helper::save( $order );
		$order->update_status( 'wc-on-hold' );
		foreach ( $order->get_items( 'line_item' ) as $item ) {
			$product_description = Product_Helper::get_product_description( $item );
		}

		$order_sync = new Order_Sync();

		$this->assertSame(
			$order_sync->get_orders(), array(
				0 => array(
					'reference' => '' . Order_Helper::get_id( $order ),
					'recipient' => array(
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
					'products'  => array(
						0 => array(
							'weight'      => 6.0,
							'quantity'    => 5,
							'description' => $product_description,
						),
					),
				),
			)
		);
	}
}
