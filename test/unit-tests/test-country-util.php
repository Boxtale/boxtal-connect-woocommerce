<?php
/**
 * Country util tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalConnectWoocommerce\Util\Country_Util;


/**
 * Class BW_Test_Country_Util.
 */
class BW_Test_Country_Util extends WP_UnitTestCase {

	/**
	 * Test get_activated_countries function.
	 */
	public function test_get_activated_countries() {
		$this->assertSame( get_class( Country_Util::get_activated_countries() ), 'WC_Countries' );
		$this->assertTrue( method_exists( Country_Util::get_activated_countries(), 'get_address_fields' ) );
	}
}
