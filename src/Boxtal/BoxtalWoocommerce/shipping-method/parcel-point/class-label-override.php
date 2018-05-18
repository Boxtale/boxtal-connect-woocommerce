<?php
/**
 * Contains code for the label override class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point;

use Boxtal\BoxtalWoocommerce\Util\Misc_Util;

/**
 * Label_Override class.
 *
 * Adds relay map link if configured.
 *
 * @class       Label_Override
 * @package     Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point
 * @category    Class
 * @author      API Boxtal
 */
class Label_Override {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'change_shipping_label' ), 10, 2 );
	}

	/**
	 * Add relay map link to shipping method label.
	 *
	 * @param string           $full_label shipping method label.
	 * @param WC_Shipping_Rate $method shipping rate.
	 * @return string $full_label
	 */
	public function change_shipping_label( $full_label, $method ) {
		if ( Misc_Util::should_display_parcel_point_link( $method ) ) {
			$full_label .= '<br/><span class="bw-select-parcel">' . __( 'Choose a parcel point', 'boxtal-woocommerce' ) . '</span>';
			if ( WC()->session ) {
				$chosen_pickup_point = WC()->session->get( 'bw_pickup_point_name_' . $method->id, false );
				if ( false !== $chosen_pickup_point ) {
					$full_label .= '<br/><span>' . __( 'Selected:', 'boxtal-woocommerce' ) . ' <span class="bw-parcel-client">' . $chosen_pickup_point . '</span></span>';
				}
			}
		}
		return $full_label;
	}
}
