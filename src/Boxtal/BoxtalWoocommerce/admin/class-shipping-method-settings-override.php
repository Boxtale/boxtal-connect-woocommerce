<?php
/**
 * Contains code for the shipping method settings override class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Admin
 */

namespace Boxtal\BoxtalWoocommerce\Admin;

/**
 * Shipping_Method_Settings_Override class.
 *
 * Add tag setting to shipping methods.
 *
 * @class       Shipping_Method_Settings_Override
 * @package     Boxtal\BoxtalWoocommerce\Admin
 * @category    Class
 * @author      API Boxtal
 */
class Shipping_Method_Settings_Override {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		$shipping_methods = WC()->shipping->get_shipping_methods();
		foreach ( $shipping_methods as $shipping_method ) {
			add_filter( 'woocommerce_shipping_instance_form_fields_' . $shipping_method->id, array( $this, 'add_form_field' ) );
		}

	}

	/**
	 * Add custom form fields to shipping methods.
	 *
	 * @param array $form_fields existing form fields.
	 * @return array $form_fields
	 */
	public function add_form_field( $form_fields ) {
		$form_fields['bw_tag_category']        = array(
			'title'       => __( 'Carrier Type', 'boxtal-woocommerce' ),
			'type'        => 'select',
			'description' => __( 'Enables relay point map display in shop and/or carrier preselection in Boxtal shipping center.', 'boxtal-woocommerce' ),
			'options'     => array(
				'express' => __( 'Express', 'boxtal-woocommerce' ),
				'home'    => __( 'Home', 'boxtal-woocommerce' ),
				'relay'   => __( 'Relay point', 'boxtal-woocommerce' ),
			),
			'class'       => 'wc-enhanced-select bw-tag-category-dropdown',
		);
		$form_fields['bw_tag_relay_operators'] = array(
			'title'       => __( 'Relay points to display', 'boxtal-woocommerce' ),
			'type'        => 'multiselect',
			'description' => __( 'Choose which relay points should be displayed for this shipping method.', 'boxtal-woocommerce' ),
			'options'     => array(
				'MONR' => __( 'Mondial Relay', 'boxtal-woocommerce' ),
				'SOGP' => __( 'Relay colis', 'boxtal-woocommerce' ),
			),
			'class'       => 'wc-enhanced-select bw-tag-relay-operators-dropdown',
		);
		return $form_fields;
	}
}
