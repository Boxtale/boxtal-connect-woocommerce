<?php
/**
 * Order util tests
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Util\Order_Util;


/**
 * Class BW_Test_Order_Util.
 */
class BW_Test_Order_Util extends WP_UnitTestCase {

	/**
	 * Test add product.
	 */
	public function test_add_product() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		$product    = WC_Helper_Product::create_simple_product();
		Order_Util::add_product( $order, $product, 4 );
		$this->assertEquals( $order->get_item_count(), 4 );
	}

	/**
	 * Test getter and setter for shipping first name.
	 */
	public function test_shipping_first_name() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_shipping_first_name( $order, 'Jon' );
		$this->assertEquals( Order_Util::get_shipping_first_name( $order ), 'Jon' );
	}

	/**
	 * Test getter and setter for shipping last name.
	 */
	public function test_shipping_last_name() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_shipping_last_name( $order, 'Snow' );
		$this->assertEquals( Order_Util::get_shipping_last_name( $order ), 'Snow' );
	}

	/**
	 * Test getter and setter for shipping company.
	 */
	public function test_shipping_company() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_shipping_company( $order, 'GoT' );
		$this->assertEquals( Order_Util::get_shipping_company( $order ), 'GoT' );
	}

	/**
	 * Test getter and setter for shipping address 1.
	 */
	public function test_shipping_address_1() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_shipping_address_1( $order, 'House Stark' );
		$this->assertEquals( Order_Util::get_shipping_address_1( $order ), 'House Stark' );
	}

	/**
	 * Test getter and setter for shipping address 2.
	 */
	public function test_shipping_address_2() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_shipping_address_2( $order, 'Winterfell' );
		$this->assertEquals( Order_Util::get_shipping_address_2( $order ), 'Winterfell' );
	}

	/**
	 * Test getter and setter for shipping city.
	 */
	public function test_shipping_city() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_shipping_city( $order, 'Paris' );
		$this->assertEquals( Order_Util::get_shipping_city( $order ), 'Paris' );
	}

	/**
	 * Test getter and setter for shipping state.
	 */
	public function test_shipping_state() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_shipping_state( $order, '' );
		$this->assertEquals( Order_Util::get_shipping_state( $order ), '' );
	}

	/**
	 * Test getter and setter for shipping postcode.
	 */
	public function test_shipping_postcode() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_shipping_postcode( $order, '75009' );
		$this->assertEquals( Order_Util::get_shipping_postcode( $order ), '75009' );
	}

	/**
	 * Test getter and setter for shipping country.
	 */
	public function test_shipping_country() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_shipping_country( $order, 'FR' );
		$this->assertEquals( Order_Util::get_shipping_country( $order ), 'FR' );
	}

	/**
	 * Test getter and setter for billing phone.
	 */
	public function test_billing_phone() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_billing_phone( $order, '0612341234' );
		$this->assertEquals( Order_Util::get_billing_phone( $order ), '0612341234' );
	}

	/**
	 * Test getter and setter for billing email.
	 */
	public function test_billing_email() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::set_billing_email( $order, 'jsnow@boxtal.com' );
		Order_Util::save( $order );
		$updated_order = new WC_Order( Order_Util::get_id( $order ) );
		$this->assertEquals( Order_Util::get_billing_email( $updated_order ), 'jsnow@boxtal.com' );
	}

	/**
	 * Test getter and setter for meta data.
	 */
	public function test_meta_data() {
		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );
		Order_Util::add_meta_data( $order, 'metaKey', 'metaValue' );
		Order_Util::save( $order );
		$updated_order = new WC_Order( Order_Util::get_id( $order ) );
		$this->assertEquals( Order_Util::get_meta( $updated_order, 'metaKey' ), 'metaValue' );
	}

    /**
     * Test get import status list.
     */
    public function test_get_import_status_list() {
        $this->assertTrue( in_array('on-hold', Order_Util::get_import_status_list()));
        $this->assertTrue( in_array('processing', Order_Util::get_import_status_list()));
    }
}
