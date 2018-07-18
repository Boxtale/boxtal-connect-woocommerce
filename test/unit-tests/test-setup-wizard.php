<?php
/**
 * Setup wizard tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Notice\Setup_Wizard_Notice;
use Boxtal\BoxtalWoocommerce\Util\Customer_Util;


/**
 * Class BW_Test_Setup_Wizard.
 */
class BW_Test_Setup_Wizard extends WP_UnitTestCase {

	/**
	 * Test get connect url.
	 */
	public function test_get_signup_url() {
		$setup_wizard_notice = new Setup_Wizard_Notice( 'setup-wizard' );
		update_option('BW_SIGNUP_URL', 'http://anyurl');
		$admins = get_super_admins();
		if ( is_array( $admins ) && count( $admins ) > 0 ) {
			$admin_user_login = array_shift( $admins );
			$admin_user       = get_user_by( 'login', $admin_user_login );
			$admin_user_id    = $admin_user->get( 'ID' );
		} else {
			$admin_user_id = 1;
		}

		$customer = new \WC_Customer( $admin_user_id );
		Customer_Util::set_email( $customer, 'jsnow@got.com' );
        Customer_Util::save( $customer );
        update_option( 'siteurl', 'http://xxx.com' );
		$this->assertSame(
			$setup_wizard_notice->get_signup_url(),
			'http://anyurl?email=jsnow%40got.com&shopUrl=http%3A%2F%2Fxxx.com&shopType=woocommerce'
		);
	}
}
