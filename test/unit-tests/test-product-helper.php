<?php
/**
 * Product helper tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Helpers\Product_Helper;


/**
 * Class BW_Test_Product_Helper.
 */
class BW_Test_Product_Helper extends WC_Unit_Test_Case {

	/**
	 * Test getter for product weight.
	 */
	public function test_get_product_weight() {
		$product = WC_Helper_Product::create_simple_product();
		Product_Helper::set_weight( $product, 2.5 );
		Product_Helper::save( $product );
		$this->assertEquals( Product_Helper::get_product_weight( Product_Helper::get_id( $product ) ), 2.50 );
	}

	/**
	 * Test setter for product weight.
	 */
	public function test_set_weight() {
		$product = WC_Helper_Product::create_simple_product();
		Product_Helper::set_weight( $product, 2.5 );
		$this->assertEquals( $product->get_weight(), 2.5 );
	}

	/**
	 * Test getter and setter for product name.
	 */
	public function test_product_name() {
		$product = WC_Helper_Product::create_simple_product();
		Product_Helper::set_name( $product, 'test' );
		Product_Helper::save( $product );
		$this->assertEquals( Product_Helper::get_name( $product ), 'test' );
	}
}
