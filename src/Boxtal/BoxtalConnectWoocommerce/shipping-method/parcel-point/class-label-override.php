<?php
/**
 * Contains code for the label override class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point;

use Boxtal\BoxtalConnectWoocommerce\Util\Misc_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Shipping_Rate_Util;

/**
 * Label_Override class.
 *
 * Adds relay map link if configured.
 *
 * @class       Label_Override
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point
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
				$chosen_parcel_point = Controller::get_chosen_point( Shipping_Rate_Util::get_id( $method ) );
				if ( null === $chosen_parcel_point ) {
					$closest_parcel_point = Controller::get_closest_point( Shipping_Rate_Util::get_id( $method ) );
					//phpcs:ignore
					$full_label          .= '<br/><span class="bw-parcel-client">' . __( 'Closest parcel point:', 'boxtal-connect' ) . ' <span class="bw-parcel-name">' . $closest_parcel_point->name . '</span></span>';
				} else {
                    //phpcs:ignore
					$full_label .= '<br/><span class="bw-parcel-client">' . __( 'Your parcel point:', 'boxtal-connect' ) . ' <span class="bw-parcel-name">' . $chosen_parcel_point->name . '</span></span>';
				}
				$full_label .= '<br/><span class="bw-select-parcel">' . __( 'Choose another', 'boxtal-connect' ) . '</span>';
			}
		}
		return $full_label;
	}
}
