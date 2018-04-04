<?php
/**
 * Helper functions tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Helpers\Helper_Functions;


/**
 * Class BW_Test_Helper_Functions.
 */
class BW_Test_Helper_Functions extends WC_Unit_Test_Case {

	/**
	 * Test not_empty_or_null function.
	 */
	public function test_not_empty_or_null() {
		$test1 = 'test';
		$this->assertEquals( Helper_Functions::not_empty_or_null( $test1 ), 'test' );
		$test2 = '';
		$this->assertEquals( Helper_Functions::not_empty_or_null( $test2 ), null );
	}
}
