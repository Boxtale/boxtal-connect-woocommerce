<?php
/**
 * Product util tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Util\Product_Util;


/**
 * Class BW_Test_Product_Util.
 */
class BW_Test_Product_Util extends WP_UnitTestCase {

	/**
	 * Test getter for product weight.
	 */
	public function test_get_product_weight() {
		$product = WC_Helper_Product::create_simple_product();
		Product_Util::set_weight( $product, 2.5 );
		Product_Util::save( $product );
		$this->assertEquals( Product_Util::get_product_weight( Product_Util::get_id( $product ) ), 2.50 );
	}

	/**
	 * Test setter for product weight.
	 */
	public function test_set_weight() {
		$product = WC_Helper_Product::create_simple_product();
		Product_Util::set_weight( $product, 2.5 );
		$this->assertEquals( $product->get_weight(), 2.5 );
	}

	/**
	 * Test getter and setter for product name.
	 */
	public function test_product_name() {
		$product = WC_Helper_Product::create_simple_product();
		Product_Util::set_name( $product, 'test' );
		Product_Util::save( $product );
		$this->assertEquals( Product_Util::get_name( $product ), 'test' );
	}
}
