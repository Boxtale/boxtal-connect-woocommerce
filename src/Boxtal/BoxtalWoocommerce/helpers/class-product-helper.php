<?php
/**
 * Contains code for product helper class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 */

namespace Boxtal\BoxtalWoocommerce\Helpers;

/**
 * Product helper class.
 *
 * Helper to manage consistency between woocommerce versions product getters and setters.
 *
 * @class       Product_Helper
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 * @category    Class
 * @author      API Boxtal
 */
class Product_Helper {
	/**
	 * Get product weight from product id.
	 *
	 * @param integer $product_id woocommerce product id.
	 * @return mixed $weight
	 */
	public static function get_product_weight( $product_id ) {
		if ( isset( $product_id ) && ! empty( $product_id ) ) {
			$product = self::get_product( $product_id );
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
	public static function get_product_description( $item ) {
		$variation_id = $item['variation_id'];
		$check_id     = ( '0' === $variation_id || 0 === $variation_id ) ? $item['product_id'] : $variation_id;
		$product      = self::get_product( $check_id );
		$description  = self::get_name( $product );
		// add attributes to title for variations.
		$product_type = self::get_product_type( $product );
		if ( 'variation' === $product_type ) {
			$parent_id      = $product->parent->id;
			$parent_product = self::get_product( $parent_id );
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
	private static function get_product_type( $product ) {
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
	 * @return WC_Product_Simple $product
	 */
	private static function get_product( $product_id ) {
		if ( function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $product_id );
		} else {
			// fix for WC < 2.5.
			$product = WC()->product_factory->get_product( $product_id );
		}
		return $product;
	}

	/**
	 * Set WC product weight.
	 *
	 * @param WC_Product_Simple $product woocommerce product.
	 * @param float             $weight desired weight.
	 * @void
	 */
	public static function set_weight( $product, $weight ) {
		if ( method_exists( $product, 'set_weight' ) ) {
			$product->set_weight( $weight );
		} else {
			update_post_meta( $product->id, '_weight', $weight );
		}
	}

	/**
	 * Get product name.
	 *
	 * @param WC_Product_Simple $product woocommerce product.
	 * @return string $name
	 */
	private static function get_name( $product ) {
		if ( method_exists( $product, 'get_name' ) ) {
			return $product->get_name();
		}
		return $product->name;
	}

	/**
	 * Set WC product name.
	 *
	 * @param WC_Product_Simple $product woocommerce product.
	 * @param string            $name desired name.
	 * @void
	 */
	public static function set_name( $product, $name ) {
		if ( method_exists( $product, 'set_name' ) ) {
			$product->set_name( $name );
		} else {
			update_post_meta( $product->id, '_name', $name );
		}
	}

	/**
	 * Save WC product.
	 *
	 * @param WC_Product_Simple $product woocommerce product.
	 * @void
	 */
	public static function save( $product ) {
		if ( method_exists( $product, 'save' ) ) {
			$product->save();
		}
	}
}
