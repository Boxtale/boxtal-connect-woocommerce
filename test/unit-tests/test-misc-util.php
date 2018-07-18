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
		$this->assertNull( Misc_Util::not_empty_or_null( $test2 ) );
	}

    /**
     * Test get_checkout_url function.
     */
    public function test_get_checkout_url() {
        $this->assertTrue(false !== filter_var(Misc_Util::get_checkout_url(), FILTER_VALIDATE_URL));
    }

    /**
     * Test remove_query_string function.
     */
    public function test_remove_query_string() {
        $test1 = 'http://anyurl';
        $this->assertEquals(Misc_Util::remove_query_string($test1), $test1);
        $test2 = $test1 . '?test';
        $this->assertEquals(Misc_Util::remove_query_string($test2), $test1);
    }

    /**
     * Test get_settings function.
     */
    public function test_get_settings() {
        WC_Helper_Shipping::create_simple_flat_rate();
        foreach (WC()->shipping->load_shipping_methods() as $method) {
            if ('flat_rate' === $method->id) {
                $this->assertNotEmpty(Misc_Util::get_settings($method->id));
            }
        }
    }
}
