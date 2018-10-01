<?php
/**
 * Configuration util tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Util\Configuration_Util;
use Boxtal\BoxtalWoocommerce\Util\Customer_Util;


/**
 * Class BW_Test_Configuration_Util.
 */
class BW_Test_Configuration_Util extends WP_UnitTestCase {

	/**
	 * Test get onboarding link.
	 */
	public function test_get_onboarding_link() {
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
			Configuration_Util::get_onboarding_link(),
			'https://www.boxtal.com/onboarding?acceptLanguage=en_US&email=jsnow%40got.com&shopUrl=http%3A%2F%2Fxxx.com&shopType=woocommerce'
		);
	}
}
