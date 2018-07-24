<?php
/**
 * Contains code for order util class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Util
 */

namespace Boxtal\BoxtalWoocommerce\Util;

/**
 * Order util class.
 *
 * Helper to manage consistency between woocommerce versions order getters and setters.
 *
 * @class       Order_Util
 * @package     Boxtal\BoxtalWoocommerce\Util
 * @category    Class
 * @author      API Boxtal
 */
class Order_Util {

	/**
	 * Add product to WC order.
	 *
	 * @param \WC_Order         $order woocommerce order.
	 * @param WC_Product_Simple $product woocommerce product.
	 * @param integer           $quantity quantity.
	 * @void
	 */
	public static function add_product( $order, $product, $quantity ) {
		if ( class_exists( 'WC_Order_Item_Product' ) ) {
			$item = new \WC_Order_Item_Product();
			$item->set_props(
				array(
					'product'  => $product,
					'quantity' => $quantity,
					'subtotal' => wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) ),
					'total'    => wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) ),
				)
			);
			$item->save();
			$order->add_item( $item );
		} else {
			$order->add_product( $product, $quantity );
		}
	}

	/**
	 * Get id of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $id order id
	 */
	public static function get_id( $order ) {
		if ( method_exists( $order, 'get_id' ) ) {
			return $order->get_id();
		}
		return $order->id;
	}

	/**
	 * Get shipping first name of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $firstname order shipping first name
	 */
	public static function get_shipping_first_name( $order ) {
		if ( method_exists( $order, 'get_shipping_first_name' ) ) {
			return $order->get_shipping_first_name();
		}
		return $order->shipping_first_name;
	}

	/**
	 * Set shipping first name of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $name desired first name.
	 * @void
	 */
	public static function set_shipping_first_name( $order, $name ) {
		if ( method_exists( $order, 'set_shipping_first_name' ) ) {
			$order->set_shipping_first_name( $name );
		} else {
			update_post_meta( $order->id, '_shipping_first_name', $name );
		}
	}

	/**
	 * Get shipping last name of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $lastname order shipping last name
	 */
	public static function get_shipping_last_name( $order ) {
		if ( method_exists( $order, 'get_shipping_last_name' ) ) {
			return $order->get_shipping_last_name();
		}
		return $order->shipping_last_name;
	}

	/**
	 * Set shipping last name of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $name desired last name.
	 * @void
	 */
	public static function set_shipping_last_name( $order, $name ) {
		if ( method_exists( $order, 'set_shipping_last_name' ) ) {
			$order->set_shipping_last_name( $name );
		} else {
			update_post_meta( $order->id, '_shipping_last_name', $name );
		}
	}

	/**
	 * Get shipping company of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $company order shipping company
	 */
	public static function get_shipping_company( $order ) {
		if ( method_exists( $order, 'get_shipping_company' ) ) {
			return $order->get_shipping_company();
		}
		return $order->shipping_company;
	}

	/**
	 * Set shipping company of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $name desired company name.
	 * @void
	 */
	public static function set_shipping_company( $order, $name ) {
		if ( method_exists( $order, 'set_shipping_company' ) ) {
			$order->set_shipping_company( $name );
		} else {
			update_post_meta( $order->id, '_shipping_company', $name );
		}
	}

	/**
	 * Get shipping address 1 of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $address1 order shipping address 1
	 */
	public static function get_shipping_address_1( $order ) {
		if ( method_exists( $order, 'get_shipping_address_1' ) ) {
			return $order->get_shipping_address_1();
		}
		return $order->shipping_address_1;
	}

	/**
	 * Set shipping address 1 of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $address desired address 1.
	 * @void
	 */
	public static function set_shipping_address_1( $order, $address ) {
		if ( method_exists( $order, 'set_shipping_address_1' ) ) {
			$order->set_shipping_address_1( $address );
		} else {
			update_post_meta( $order->id, '_shipping_address_1', $address );
		}
	}

	/**
	 * Get shipping address 2 of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $address2 order shipping address 2
	 */
	public static function get_shipping_address_2( $order ) {
		if ( method_exists( $order, 'get_shipping_address_2' ) ) {
			return $order->get_shipping_address_2();
		}
		return $order->shipping_address_2;
	}

	/**
	 * Set shipping address 2 of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $address desired address 2.
	 * @void
	 */
	public static function set_shipping_address_2( $order, $address ) {
		if ( method_exists( $order, 'set_shipping_address_2' ) ) {
			$order->set_shipping_address_2( $address );
		} else {
			update_post_meta( $order->id, '_shipping_address_2', $address );
		}
	}

	/**
	 * Get shipping city of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $city order shipping city
	 */
	public static function get_shipping_city( $order ) {
		if ( method_exists( $order, 'get_shipping_city' ) ) {
			return $order->get_shipping_city();
		}
		return $order->shipping_city;
	}

	/**
	 * Set shipping city of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $city desired city.
	 * @void
	 */
	public static function set_shipping_city( $order, $city ) {
		if ( method_exists( $order, 'set_shipping_city' ) ) {
			$order->set_shipping_city( $city );
		} else {
			update_post_meta( $order->id, '_shipping_city', $city );
		}
	}

	/**
	 * Get shipping state of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $state order shipping state
	 */
	public static function get_shipping_state( $order ) {
		if ( method_exists( $order, 'get_shipping_state' ) ) {
			return $order->get_shipping_state();
		}
		return $order->shipping_state;
	}

	/**
	 * Set shipping state of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $state desired state.
	 * @void
	 */
	public static function set_shipping_state( $order, $state ) {
		if ( method_exists( $order, 'set_shipping_state' ) ) {
			$order->set_shipping_state( $state );
		} else {
			update_post_meta( $order->id, '_shipping_state', $state );
		}
	}

	/**
	 * Get shipping postcode of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $postcode order shipping postcode
	 */
	public static function get_shipping_postcode( $order ) {
		if ( method_exists( $order, 'get_shipping_postcode' ) ) {
			return $order->get_shipping_postcode();
		}
		return $order->shipping_postcode;
	}

	/**
	 * Set shipping postcode of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $postcode desired postcode.
	 * @void
	 */
	public static function set_shipping_postcode( $order, $postcode ) {
		if ( method_exists( $order, 'set_shipping_postcode' ) ) {
			$order->set_shipping_postcode( $postcode );
		} else {
			update_post_meta( $order->id, '_shipping_postcode', $postcode );
		}
	}

	/**
	 * Get shipping country of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $country order shipping country
	 */
	public static function get_shipping_country( $order ) {
		if ( method_exists( $order, 'get_shipping_country' ) ) {
			return $order->get_shipping_country();
		}
		return $order->shipping_country;
	}

	/**
	 * Set shipping country of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $country desired postcode.
	 * @void
	 */
	public static function set_shipping_country( $order, $country ) {
		if ( method_exists( $order, 'set_shipping_country' ) ) {
			$order->set_shipping_country( $country );
		} else {
			update_post_meta( $order->id, '_shipping_country', $country );
		}
	}

	/**
	 * Get billing email of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $country order billing email
	 */
	public static function get_billing_email( $order ) {
		if ( method_exists( $order, 'get_billing_email' ) ) {
			return $order->get_billing_email();
		}
		return $order->billing_email;
	}

	/**
	 * Set billing email of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $email desired email.
	 * @void
	 */
	public static function set_billing_email( $order, $email ) {
		if ( method_exists( $order, 'set_billing_email' ) ) {
			$order->set_billing_email( $email );
		} else {
			update_post_meta( $order->id, '_billing_email', $email );
		}
	}

	/**
	 * Get billing phone of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $country order billing phone
	 */
	public static function get_billing_phone( $order ) {
		if ( method_exists( $order, 'get_billing_phone' ) ) {
			return $order->get_billing_phone();
		}
		return $order->billing_phone;
	}

	/**
	 * Set billing phone of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $phone desired phone.
	 * @void
	 */
	public static function set_billing_phone( $order, $phone ) {
		if ( method_exists( $order, 'set_billing_phone' ) ) {
			$order->set_billing_phone( $phone );
		} else {
			update_post_meta( $order->id, '_billing_phone', $phone );
		}
	}

	/**
	 * Get status of WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @return string $status order status
	 */
	public static function get_status( $order ) {
		if ( method_exists( $order, 'get_status' ) ) {
			return $order->get_status();
		}
		return $order->status;
	}

	/**
	 * Save WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @void
	 */
	public static function save( $order ) {
		if ( method_exists( $order, 'save' ) ) {
			$order->save();
		}
	}

	/**
	 * Add meta data to WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $key key of meta data.
	 * @param string    $data data to be added.
	 * @void
	 */
	public static function add_meta_data( $order, $key, $data ) {
		if ( method_exists( $order, 'add_meta_data' ) ) {
			$order->add_meta_data( $key, $data );
		} else {
			update_post_meta( $order->id, $key, $data );
		}
	}

	/**
	 * Get meta data to WC order.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @param string    $key key of meta data.
	 * @void
	 */
	public static function get_meta( $order, $key ) {
		if ( method_exists( $order, 'get_meta' ) ) {
			return $order->get_meta( $key );
		}
		return get_post_meta( $order->id, $key, true );
	}

	/**
	 * Get order in admin context.
	 *
	 * @return \WC_Order $order woocommerce order
	 */
	public static function admin_get_order() {
		global $the_order, $post;
		if ( ! is_object( $the_order ) ) {
			if ( function_exists( 'wc_get_order' ) ) {
				$order = wc_get_order( $post->ID );
			} else {
				// fix for WC < 2.5.
				if ( WC()->order_factory !== false ) {
					$order = WC()->order_factory->get_order( $post->ID );
				} else {
					global $theorder;

					if ( ! is_object( $theorder ) ) {
						$theorder = new \WC_Order( $post->ID );
					}

					$order = $theorder;
				}
			}
		} else {
			$order = $the_order;
		}
		return $order;
	}

	/**
	 * Get order statuses valid for import.
	 *
	 * @return array string list of statuses
	 */
	public static function get_import_status_list() {
		$statuses            = array();
		$unauthorized_status = array( 'wc-pending', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed' );
		foreach ( wc_get_order_statuses() as $order_status => $translation ) {
			if ( ! in_array( $order_status, $unauthorized_status, true ) ) {
				$statuses[] = str_replace( 'wc-', '', $order_status );
			}
		};
		return $statuses;
	}
}
