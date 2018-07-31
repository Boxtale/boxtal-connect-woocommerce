<?php
/**
 * Environment util tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Plugin;
use Boxtal\BoxtalWoocommerce\Util\Environment_Util;


/**
 * Class BW_Test_Environment_Util.
 */
class BW_Test_Environment_Util extends WP_UnitTestCase {

	/**
	 * Test get environment warning.
	 */
	public function test_environment_errors() {
		$plugin                    = new Plugin();
		$plugin['min-wc-version']  = '2.3.0';
		$plugin['min-php-version'] = '5.3.0';
		$this->assertFalse( Environment_Util::check_errors( $plugin ) );
	}
}
