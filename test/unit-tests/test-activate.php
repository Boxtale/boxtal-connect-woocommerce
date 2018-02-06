<?php
/**
 * Activation test
 *
 * @package Boxtal\Tests
 */

/**
 * Class BW_Test_Activate.
 */
class BW_Test_Activate extends WC_Unit_Test_Case {

	/**
	 * Test hello world.
	 */
	public function test_hello_world() {
		$this->assertTrue( \Boxtal\BoxtalWoocommerce\Boxtal_Woocommerce::instance()->hello() === 'hello world' );
	}
}
