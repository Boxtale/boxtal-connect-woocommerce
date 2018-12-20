<?php
/**
 * Contains code for the settings override class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method
 */

namespace Boxtal\BoxtalConnectWoocommerce\Shipping_Method;

use Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point\Controller;

/**
 * Settings_Override class.
 *
 * Add tag setting to shipping methods.
 *
 * @class       Settings_Override
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method
 * @category    Class
 * @author      API Boxtal
 */
class Settings_Override {

	/**
	 * Construct function.
	 *
	 * @param array $plugin plugin array.
	 * @void
	 */
	public function __construct( $plugin ) {
		$this->plugin_url     = $plugin['url'];
		$this->plugin_version = $plugin['version'];
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'admin_enqueue_scripts', array( $this, 'shipping_settings_styles' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'shipping_methods_settings_override' ) );
	}

	/**
	 * Add extra field to shipping methods.
	 *
	 * @param array $shipping_methods wc shipping methods.
	 * @return array
	 */
	public function shipping_methods_settings_override( $shipping_methods ) {
		foreach ( $shipping_methods as $shipping_method => $classname ) {
			if ( 'boxtal_connect' !== $shipping_method ) {
				add_filter( 'woocommerce_shipping_instance_form_fields_' . $shipping_method, array( $this, 'add_form_field' ) );
			}
		}
		return $shipping_methods;
	}

	/**
	 * Enqueue shipping settings scripts
	 *
	 * @param string $hook hook name.
	 * @void
	 */
	public function shipping_settings_styles( $hook ) {
        // phpcs:ignore
        $current_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? urldecode( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : '';
		if ( 'woocommerce_page_wc-settings' === $hook && 'shipping' === $current_tab ) {
			wp_enqueue_style( 'bw_notices', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/shipping-method.css', array(), $this->plugin_version );
		}
	}

	/**
	 * Add custom form fields to shipping methods.
	 *
	 * @param array $form_fields existing form fields.
	 * @return array $form_fields
	 */
	public function add_form_field( $form_fields ) {
		$network_options                         = Controller::get_network_options();
		$form_fields['bw_parcel_point_networks'] = array(
			'title'       => __( 'Parcel points map display (Boxtal Connect)', 'boxtal-connect' ),
			'type'        => 'multiselect',
			'description' => __( 'Deprecated. Use the Boxtal Connect rate shipping method instead.', 'boxtal-connect' ),
			'options'     => $network_options,
			'default'     => array(),
			'class'       => 'wc-enhanced-select bw-parcel-point-networks-dropdown',
		);
		return $form_fields;
	}
}
