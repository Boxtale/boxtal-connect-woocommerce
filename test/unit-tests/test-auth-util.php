<?php
/**
 * Auth util tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Util\Auth_Util;


/**
 * Class BW_Test_Auth_Util.
 */
class BW_Test_Auth_Util extends WP_UnitTestCase {

	/**
	 * Test pair and is_plugin_paired functions.
	 */
	public function test_is_plugin_paired() {
		delete_option('BW_ACCESS_KEY');
		delete_option('BW_SECRET_KEY');
		$this->assertFalse( Auth_Util::is_plugin_paired() );
		Auth_Util::pair_plugin('test', 'test');
		$this->assertTrue( Auth_Util::is_plugin_paired() );
	}

    /**
     * Test can_use_plugin function.
     */
    public function test_can_use_plugin() {
        Auth_Util::pair_plugin('test', 'test');
        update_option('BW_PAIRING_UPDATE', 'test');
        $this->assertFalse( Auth_Util::can_use_plugin() );
        delete_option('BW_PAIRING_UPDATE');
        $this->assertTrue( Auth_Util::can_use_plugin() );
    }

    /**
     * Test start_pairing_update & end_pairing_update functions.
     */
    public function test_pairing_update() {
        $this->assertFalse( get_option('BW_PAIRING_UPDATE') );
        Auth_Util::start_pairing_update('test');
        $this->assertSame( get_option('BW_PAIRING_UPDATE'), 'test' );
        Auth_Util::end_pairing_update();
        $this->assertFalse( get_option('BW_PAIRING_UPDATE') );
    }

    /**
     * Test has_maps_token function.
     */
    public function test_has_maps_token() {
        $this->assertFalse( Auth_Util::has_maps_token() );
        WC()->session->set('bw_token', 'test');
        $this->assertTrue( Auth_Util::has_maps_token() );
    }
}
