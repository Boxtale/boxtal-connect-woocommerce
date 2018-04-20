<?php
/**
 * Setup wizard tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Notices\Setup_Wizard_Notice;
use Boxtal\BoxtalWoocommerce\Helpers\Customer_Helper;


/**
 * Class BW_Test_Setup_Wizard.
 */
class BW_Test_Setup_Wizard extends WC_Unit_Test_Case {

    /**
     * Test get connect url.
     */
    public function test_get_connect_url() {
        $setup_wizard_notice = new Setup_Wizard_Notice( 'setup-wizard' );
        $setup_wizard_notice->set_base_connect_link('http://xxx/connect-shop');
        $admins      = get_super_admins();
        if ( count( $admins > 0 ) ) {
            $admin_user_login = array_shift( $admins );
            $admin_user       = get_user_by( 'login', $admin_user_login );
            $admin_user_id    = $admin_user->get( 'ID' );
            $customer         = new \WC_Customer( $admin_user_id );
            Customer_Helper::set_first_name($customer, 'jon');
            Customer_Helper::set_last_name($customer, 'snow');
            Customer_Helper::set_email($customer, 'jsnow@got.com');
            Customer_Helper::set_billing_phone($customer, '0612341234');
            Customer_Helper::set_billing_address_1($customer, 'Castle');
            Customer_Helper::set_billing_address_2($customer, 'Black');
            Customer_Helper::set_billing_city($customer, 'Winterfell');
            Customer_Helper::set_billing_postcode($customer, '01234');
            Customer_Helper::set_billing_state($customer, '');
            Customer_Helper::set_billing_country($customer, 'FR');
            update_option('siteurl', 'http://xxx.com');
            $setup_wizard_notice->set_return_url('http://xxx.com/wp-admin/');
            Customer_Helper::save($customer);
        }
        $this->assertSame(
            $setup_wizard_notice->get_connect_url(),
            'http://xxx/connect-shop?firstName=jon&lastName=snow&email=jsnow%40got.com&phone=0612341234&address=Castle+Black&city=Winterfell&postcode=01234&state=&country=FR&shopUrl=http%3A%2F%2Fxxx.com&returnUrl=http%3A%2F%2Fxxx.com%2Fwp-admin%2F'
        );
    }
}
