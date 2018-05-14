<?php
/**
 * Misc util tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Util\Misc_Util;


/**
 * Class BW_Test_Misc_Util.
 */
class BW_Test_Misc_Util extends WP_UnitTestCase {

	/**
	 * Test not_empty_or_null function.
	 */
	public function test_not_empty_or_null() {
		$test1 = 'test';
		$this->assertEquals( Misc_Util::not_empty_or_null( $test1 ), 'test' );
		$test2 = '';
		$this->assertEquals( Misc_Util::not_empty_or_null( $test2 ), null );
	}
}
