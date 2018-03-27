<?php
/**
 * Contains code for the order sync class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Api
 */

namespace Boxtal\BoxtalWoocommerce\Api;

/**
 * Order sync container class.
 *
 * Opens API endpoint to sync orders.
 *
 * @class       Order_Sync
 * @package     Boxtal\BoxtalWoocommerce\Api
 * @category    Class
 * @author      API Boxtal
 */
class Order_Sync {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'woocommerce_api_boxtal_pull_orders', array( $this, 'api_callback_handler' ) );
	}

	/**
	 * Endpoint callback.
	 *
	 * @void
	 */
	public function api_callback_handler() {
		$response = $this->get_orders();
		echo wp_json_encode( $response );
		die();
	}

	/**
	 * Get Woocommerce orders.
	 *
	 * @return WC_Order[] $result
	 */
	public function get_orders() {
		$result = array();
		foreach ( wc_get_orders( array() ) as $order ) {
			$recipient = array(
				'firstname' => $order->get_shipping_first_name(),
				'lastname'  => $order->get_shipping_last_name(),
				'company'   => $order->get_shipping_company(),
				'address'   => $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2(),
				'city'      => $order->get_shipping_city(),
				'state'     => $order->get_shipping_state(),
				'postcode'  => $order->get_shipping_postcode(),
				'country'   => $order->get_shipping_country(),
				'phone'     => $order->get_billing_phone(),
				'email'     => $order->get_billing_email(),
			);
			$products  = array();
			foreach ( $order->get_items( 'line_item' ) as $item ) {
				$product                = array();
				$product_id             = 0 !== $item['variation_id'] ? $item['variation_id'] : $item['product_id'];
				$product['weight']      = false !== $this->get_product_weight( $product_id ) ? (float) $this->get_product_weight( $product_id ) : null;
				$product['quantity']    = (int) $item['qty'];
				$product['description'] = esc_html( $this->get_product_description( $item ) );
				$products[]             = $product;
			}
			$result[] = array(
				'recipient' => $recipient,
				'products'  => $products,
			);
		}
		return $result;
	}

	/**
	 * Get product weight from product id.
	 *
	 * @param integer $product_id woocommerce product id.
	 * @return mixed $weight
	 */
	private function get_product_weight( $product_id ) {
		if ( isset( $product_id ) && ! empty( $product_id ) ) {
			$product = $this->get_product( $product_id );
			if ( $product->get_weight() && $product->get_weight() !== '' ) {
				$weight = wc_format_decimal( wc_get_weight( $product->get_weight(), 'kg' ), 2 );
			} else {
				return false;
			}
			return $weight;
		}
	}

	/**
	 * Get product description.
	 *
	 * @param WC_Order_Item $item woocommerce order item.
	 * @return string $description
	 */
	private function get_product_description( $item ) {
		$check_id    = 0 === $item['variation_id'] ? $item['product_id'] : $item['variation_id'];
		$product     = $this->get_product( $check_id );
		$description = $product->get_name();
		// add attributes to title for variations.
		$product_type = $this->get_product_type( $product );
		if ( 'variation' === $product_type ) {
			$parent_id      = $product->parent->id;
			$parent_product = $this->get_product( $parent_id );
			foreach ( $parent_product->get_available_variations() as $variation ) {
				if ( $variation['variation_id'] === $check_id ) {
					foreach ( $variation['attributes'] as $attributes ) {
						$description .= ' ' . $attributes;
					}
				}
			}
		}
		return $description;
	}

	/**
	 * Get product type. Fix for WC > 3.1.
	 *
	 * @param WC_Product_Simple $product woocommerce product.
	 * @return string $product_type
	 */
	private function get_product_type( $product ) {
		if ( method_exists( $product, 'get_type' ) ) {
			return $product->get_type();
		} else {
			return $product->product_type;
		}
	}

	/**
	 * Get WC product from product id. Fix for WC > 2.5.
	 *
	 * @param integer $product_id woocommerce product id.
	 * @return WC_Product_simple $product
	 */
	private function get_product( $product_id ) {
		if ( function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $product_id );
		} else {
			// fix for WC < 2.5.
			$product = WC()->product_factory->get_product( $product_id );
		}
		return $product;
	}
}
