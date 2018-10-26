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

		$shipping_methods = WC()->shipping->get_shipping_methods();
		foreach ( $shipping_methods as $shipping_method ) {
			add_filter( 'woocommerce_shipping_instance_form_fields_' . $shipping_method->id, array( $this, 'add_form_field' ) );
		}

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
			'description' => __( 'Choose one or more parcel point networks in order to display a parcel point map for this shipping method. Use ctrl+click to select several networks.', 'boxtal-connect' ),
			'options'     => $network_options,
			'default'     => array(),
			'class'       => 'wc-enhanced-select bw-parcel-point-networks-dropdown',
		);
		return $form_fields;
	}
}
