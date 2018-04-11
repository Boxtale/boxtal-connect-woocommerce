<?php
/**
 * Notices tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Admin\Notices;


/**
 * Class BW_Test_Notices.
 */
class BW_Test_Notices extends WC_Unit_Test_Case {

	/**
	 * Mock plugin container values.
	 *
	 * @var array
	 */
	private $plugin = array(
		'url'     => '',
		'version' => '0.1.0',
	);

	/**
	 * Test add & remove notice.
	 */
	public function test_add_remove_notice() {
		$notices = new Notices( $this->plugin );
		$notices->add_notice( 'pair' );
		$stored_notices = get_option( 'BW_NOTICES' );
		$this->assertSame(
			$stored_notices,
			array(
				0 => 'pair',
			)
		);
		$notices->remove_notice( 'pair' );
		$stored_notices = get_option( 'BW_NOTICES' );
		$this->assertEmpty( $stored_notices );
	}

	/**
	 * Test autodestruct notice.
	 */
	public function test_autodestruct_notice() {
		$notices = new Notices( $this->plugin );
		$notices->add_notice( 'custom' );
		$stored_notices = $notices->get_notices();
		foreach ( $stored_notices as $notice ) {
			$notice->render();
		}
		$stored_notices = $notices->get_notices();
		$this->assertEmpty( $stored_notices );
	}
}
