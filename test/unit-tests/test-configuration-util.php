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
		update_option( 'admin_email', 'jsnow@got.com' );
		update_option( 'siteurl', 'http://xxx.com' );
		$this->assertSame(
			Configuration_Util::get_onboarding_link(),
			'https://www.boxtal.com/onboarding?acceptLanguage=en_US&email=jsnow%40got.com&shopUrl=http%3A%2F%2Fxxx.com&shopType=woocommerce'
		);
	}
}
