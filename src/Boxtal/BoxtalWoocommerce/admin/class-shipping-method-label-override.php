<?php
/**
 * Contains code for the shipping method label override class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Admin
 */

namespace Boxtal\BoxtalWoocommerce\Admin;

use Boxtal\BoxtalWoocommerce\Helpers\Helper_Functions;

/**
 * Shipping_Method_Label_Override class.
 *
 * Adds relay map link if configured.
 *
 * @class       Shipping_Method_Label_Override
 * @package     Boxtal\BoxtalWoocommerce\Admin
 * @category    Class
 * @author      API Boxtal
 */
class Shipping_Method_Label_Override {

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
		if ( Helper_Functions::should_display_parcel_point_link( $method ) ) {
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
