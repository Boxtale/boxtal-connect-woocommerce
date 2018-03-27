<?php
/**
 * Order sync test
 *
 * @package Boxtal\Tests
 */

use Boxtal\BoxtalWoocommerce\Api\Order_Sync;

/**
 * Class BW_Test_Order_Sync.
 */
class BW_Test_Order_Sync extends WC_Unit_Test_Case {


	/**
	 * Test api callback.
	 */
	public function test_get_orders() {
		WC_Helper_Shipping::create_simple_flat_rate();

		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);
		$order      = wc_create_order( $order_data );

        $product = WC_Helper_Product::create_simple_product();
        if (method_exists($product, 'set_weight')) {
            $product->set_weight( 2.5 );
        } else {
            $product->weight = 2.5;
        }
        if (method_exists($product, 'set_name')) {
            $product->set_name( 'simple product' );
        } else {
            $product->name = 'simple product';
        }
        if (method_exists($product, 'save')) {
            $product->save();
        }

        if (class_exists('WC_Order_Item_Product')) {
            $item    = new WC_Order_Item_Product();
            $item->set_props(
                array(
                    'product'  => $product,
                    'quantity' => 4,
                    'subtotal' => wc_get_price_excluding_tax( $product, array( 'qty' => 4 ) ),
                    'total'    => wc_get_price_excluding_tax( $product, array( 'qty' => 4 ) ),
                )
            );
            $item->save();
            $order->add_item( $item );
        } else {
            $order->add_product( $product, 4);
        }

        if (method_exists($order, 'set_shipping_first_name')) {
            $order->set_shipping_first_name( 'Jon' );
        } else {
            $order->shipping_first_name = 'Jon';
        }
        if (method_exists($order, 'set_shipping_last_name')) {
            $order->set_shipping_last_name( 'Snow' );
        } else {
            $order->shipping_last_name = 'Snow';
        }
        if (method_exists($order, 'set_shipping_company')) {
            $order->set_shipping_company( 'GoT' );
        } else {
            $order->shipping_company = 'GoT';
        }
        if (method_exists($order, 'set_shipping_address_1')) {
            $order->set_shipping_address_1( 'House Stark' );
        } else {
            $order->shipping_address_1 = 'House Stark';
        }
        if (method_exists($order, 'set_shipping_address_2')) {
            $order->set_shipping_address_2( 'Winterfell' );
        } else {
            $order->shipping_address_2 = 'Winterfell';
        }
        if (method_exists($order, 'set_shipping_city')) {
            $order->set_shipping_city( 'Paris' );
        } else {
            $order->shipping_city = 'Paris';
        }
        if (method_exists($order, 'set_shipping_state')) {
            $order->set_shipping_state( '' );
        } else {
            $order->shipping_state = '';
        }
        if (method_exists($order, 'set_shipping_postcode')) {
            $order->set_shipping_postcode( '75009' );
        } else {
            $order->shipping_postcode = '75009';
        }
        if (method_exists($order, 'set_shipping_country')) {
            $order->set_shipping_country( 'FR' );
        } else {
            $order->shipping_country = 'FR';
        }
        if (method_exists($order, 'set_billing_email')) {
            $order->set_billing_email( 'jsnow@boxtal.com' );
        } else {
            $order->billing_email = 'jsnow@boxtal.com';
        }
        if (method_exists($order, 'set_billing_phone')) {
            $order->set_billing_phone( '0612341234' );
        } else {
            $order->billing_phone = '0612341234';
        }
        if (method_exists($order, 'save')) {
            $order->save();
        }

		$order_sync = new Order_Sync();

		$this->assertSame(
			$order_sync->get_orders(), array(
				0 => array(
					'recipient' => array(
						'firstname' => 'Jon',
						'lastname'  => 'Snow',
						'company'   => 'GoT',
						'address'   => 'House Stark Winterfell',
						'city'      => 'Paris',
						'state'     => '',
						'postcode'  => '75009',
						'country'   => 'FR',
						'phone'     => '0612341234',
						'email'     => 'jsnow@boxtal.com',
					),
					'products'  => array(
						0 => array(
							'weight'      => 2.5,
							'quantity'    => 4,
							'description' => 'simple product',
						),
					),
				),
			)
		);
	}
}
