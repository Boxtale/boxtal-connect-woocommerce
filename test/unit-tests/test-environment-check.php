<?php
/**
 * Environment check tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Config\Environment_Check;
use Boxtal\BoxtalWoocommerce\Admin\Notices;


/**
 * Class BW_Test_Environment_Check.
 */
class BW_Test_Environment_Check extends WC_Unit_Test_Case {

	/**
	 * Mock plugin container values.
	 *
	 * @var array
	 */
	private $plugin = array(
		'min-php-version' => '5.3.0',
		'min-wc-version'  => '2.3.0',
		'url'             => '',
		'version'         => '0.1.0',
	);

	/**
	 * Contruct function.
	 */
	public function __construct() {
		$this->plugin['notices'] = new Notices( $this->plugin );
	}

	/**
	 * Test get environment warning.
	 */
	public function test_get_environment_warning() {
		//$environment_check = new Environment_Check( $this->plugin );
		$this->assertEquals(
			false, false
		);
	}
}
