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
	 * @param string            $full_label shipping method label.
	 * @param \WC_Shipping_Rate $method shipping rate.
	 * @return string $full_label
	 */
	public function change_shipping_label( $full_label, $method ) {
		if ( Misc_Util::should_display_parcel_point_link( $method ) ) {
			$points_response = Controller::init_points( Controller::get_recipient_address(), $method );
			if ( $points_response ) {
				$chosen_parcel_point = Controller::get_chosen_point( $method );
				if ( $chosen_parcel_point === null ) {
					$closest_parcel_point = Controller::get_closest_point( $method );
					$full_label          .= '<br/><span>' . __( 'Closest parcel point:', 'boxtal-woocommerce' ) . ' <span class="bw-parcel-client">' . $closest_parcel_point->label . '</span></span>';
				} else {
					$full_label .= '<br/><span>' . __( 'Your parcel point:', 'boxtal-woocommerce' ) . ' <span class="bw-parcel-client">' . $chosen_parcel_point->label . '</span></span>';
				}
				$full_label .= '<br/><span class="bw-select-parcel">' . __( 'Choose another', 'boxtal-woocommerce' ) . '</span>';
			}
		}
		return $full_label;
	}
}
