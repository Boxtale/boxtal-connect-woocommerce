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
	 * Test add & remove notice.
	 */
	public function test_add_remove_notice() {
		Notices::remove_all_notices();
		Notices::add_notice( 'setup-wizard' );
		$stored_notices = get_option( 'BW_NOTICES' );
		$this->assertSame(
			$stored_notices,
			array(
				0 => 'setup-wizard',
			)
		);
		Notices::remove_notice( 'setup-wizard' );
		$stored_notices = get_option( 'BW_NOTICES' );
		$this->assertEmpty( $stored_notices );
	}

	/**
	 * Test autodestruct notice.
	 */
	public function test_autodestruct_notice() {
		Notices::remove_all_notices();
		Notices::add_notice( 'custom' );
		$stored_notices = Notices::get_notices();
		foreach ( $stored_notices as $notice ) {
			$notice->render();
		}
		$stored_notices = Notices::get_notices();
		$this->assertEmpty( $stored_notices );
	}
}
