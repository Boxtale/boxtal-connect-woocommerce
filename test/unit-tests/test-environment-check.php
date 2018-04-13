<?php
/**
 * Environment check tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Config\Environment_Check;


/**
 * Class BW_Test_Environment_Check.
 */
class BW_Test_Environment_Check extends WC_Unit_Test_Case {

	/**
	 * Test get environment warning.
	 */
	public function test_get_environment_warning() {
		$environment_check = new Environment_Check( $this->plugin );
		$this->assertFalse(
			$environment_check->boxtal_woocommerce_get_environment_warning()
		);
	}
}
