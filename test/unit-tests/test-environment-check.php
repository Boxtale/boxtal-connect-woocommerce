<?php
/**
 * Environment check tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Init\Environment_Check;
use Boxtal\BoxtalWoocommerce\Plugin;


/**
 * Class BW_Test_Environment_Check.
 */
class BW_Test_Environment_Check extends WP_UnitTestCase {

	/**
	 * Test get environment warning.
	 */
	public function test_get_environment_warning() {
		$plugin                    = new Plugin();
		$plugin['min-wc-version']  = '2.3.0';
		$plugin['min-php-version'] = '5.3.0';
		$environment_check         = new Environment_Check( $plugin );
		$this->assertFalse(
			$environment_check->boxtal_woocommerce_get_environment_warning()
		);
	}
}
