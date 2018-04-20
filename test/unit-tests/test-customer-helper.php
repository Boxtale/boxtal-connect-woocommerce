<?php
/**
 * Customer helper tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Helpers\Customer_Helper;


/**
 * Class BW_Test_Customer_Helper.
 */
class BW_Test_Customer_Helper extends WC_Unit_Test_Case {

	/**
	 * Test getter and setter for first name.
	 */
	public function test_first_name() {
		$customer = new \WC_Customer();
		Customer_Helper::set_first_name( $customer, 'jon' );
		$this->assertEquals( Customer_Helper::get_first_name( $customer ), 'jon' );
	}

	/**
	 * Test getter and setter for last name.
	 */
	public function test_last_name() {
		$customer = new \WC_Customer();
		Customer_Helper::set_last_name( $customer, 'snow' );
		$this->assertEquals( Customer_Helper::get_last_name( $customer ), 'snow' );
	}

	/**
	 * Test getter and setter for email.
	 */
	public function test_email() {
		$customer = new \WC_Customer();
		Customer_Helper::set_email( $customer, 'jsnow@got.com' );
		$this->assertEquals( Customer_Helper::get_email( $customer ), 'jsnow@got.com' );
	}

	/**
	 * Test getter and setter for billing phone.
	 */
	public function test_billing_phone() {
		$customer = new \WC_Customer();
		Customer_Helper::set_billing_phone( $customer, '0612341234' );
		$this->assertEquals( Customer_Helper::get_billing_phone( $customer ), '0612341234' );
	}

	/**
	 * Test getter and setter for billing address 1.
	 */
	public function test_billing_address_1() {
		$customer = new \WC_Customer();
		Customer_Helper::set_billing_address_1( $customer, 'Castle' );
		$this->assertEquals( Customer_Helper::get_billing_address_1( $customer ), 'Castle' );
	}

	/**
	 * Test getter and setter for billing address 2.
	 */
	public function test_billing_address_2() {
		$customer = new \WC_Customer();
		Customer_Helper::set_billing_address_2( $customer, 'Black' );
		$this->assertEquals( Customer_Helper::get_billing_address_2( $customer ), 'Black' );
	}

	/**
	 * Test getter and setter for billing city.
	 */
	public function test_billing_city() {
		$customer = new \WC_Customer();
		Customer_Helper::set_billing_city( $customer, 'Winterfell' );
		$this->assertEquals( Customer_Helper::get_billing_city( $customer ), 'Winterfell' );
	}

	/**
	 * Test getter and setter for billing postcode.
	 */
	public function test_billing_postcode() {
		$customer = new \WC_Customer();
		Customer_Helper::set_billing_postcode( $customer, '01234' );
		$this->assertEquals( Customer_Helper::get_billing_postcode( $customer ), '01234' );
	}

	/**
	 * Test getter and setter for billing state.
	 */
	public function test_billing_state() {
		$customer = new \WC_Customer();
		Customer_Helper::set_billing_state( $customer, 'NY' );
		$this->assertEquals( Customer_Helper::get_billing_state( $customer ), 'NY' );
	}

	/**
	 * Test getter and setter for billing country.
	 */
	public function test_billing_country() {
		$customer = new \WC_Customer();
		Customer_Helper::set_billing_country( $customer, 'FR' );
		$this->assertEquals( Customer_Helper::get_billing_country( $customer ), 'FR' );
	}
}
