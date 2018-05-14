<?php
/**
 * Notice controller tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Notice\Notice_Controller;


/**
 * Class BW_Test_Notice_Controller.
 */
class BW_Test_Notice_Controller extends WP_UnitTestCase {

	/**
	 * Test add & remove notice.
	 */
	public function test_add_remove_notice() {
		Notice_Controller::remove_all_notices();
		Notice_Controller::add_notice( 'setup-wizard' );
		$stored_notices = get_option( 'BW_NOTICES' );
		$this->assertSame(
			$stored_notices,
			array(
				0 => 'setup-wizard',
			)
		);
		Notice_Controller::remove_notice( 'setup-wizard' );
		$stored_notices = get_option( 'BW_NOTICES' );
		$this->assertEmpty( $stored_notices );
	}

	/**
	 * Test autodestruct notice.
	 */
	public function test_autodestruct_notice() {
		Notice_Controller::remove_all_notices();
		Notice_Controller::add_notice( 'custom' );
		$stored_notices = Notice_Controller::get_notices();
		foreach ( $stored_notices as $notice ) {
			$notice->render();
		}
		$stored_notices = Notice_Controller::get_notices();
		$this->assertEmpty( $stored_notices );
	}
}
