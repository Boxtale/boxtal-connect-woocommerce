<?php
/**
 * Contains code for the scripts class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Includes
 */

namespace Boxtal\BoxtalWoocommerce\Includes;

use Boxtal\BoxtalWoocommerce\Admin\Notices;
use Boxtal\BoxtalWoocommerce\Helpers\Helper_Functions;

/**
 * Scripts class.
 *
 * Adds scripts.
 *
 * @class       Scripts
 * @package     Boxtal\BoxtalWoocommerce\Includes
 * @category    Class
 * @author      API Boxtal
 */
class Scripts {

	/**
	 * Construct function.
	 *
	 * @param array $plugin plugin array.
	 * @void
	 */
	public function __construct( $plugin ) {
		$this->plugin_url     = $plugin['url'];
		$this->plugin_version = $plugin['version'];
		$this->ajax_nonce     = wp_create_nonce( 'boxtale_woocommerce' );
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'woocommerce_after_checkout_form', array( $this, 'load_pickup_point_scripts' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'notices_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'shipping_settings_scripts' ) );
		}
	}

	/**
	 * Enqueue pickup point script
	 *
	 * @void
	 */
	public function load_pickup_point_scripts() {
		if ( ! Helper_Functions::is_checkout_url() ) {
			return;
		}

		$translations = array(
			'error' => array(
				'carrierNotFound'     => __( 'Unable to find carrier', 'boxtal-woocommerce' ),
				'googleQuotaExceeded' => __( 'Google maps API quota exceeded', 'boxtal-woocommerce' ),
				'addressNotFound'     => __( 'Could not find address', 'boxtal-woocommerce' ),
			),
			'text'  => array(
				'openingHours'        => __( 'Opening hours', 'boxtal-woocommerce' ),
				'chooseParcelPoint'   => __( 'Choose this Parcel Point', 'boxtal-woocommerce' ),
				'yourAddress'         => __( 'Your address:', 'boxtal-woocommerce' ),
				'closeMap'            => __( 'Close map', 'boxtal-woocommerce' ),
				'selectedParcelPoint' => __( 'Selected:', 'boxtal-woocommerce' ),
			),
			'day'   => array(
				1 => __( 'monday', 'boxtal-woocommerce' ),
				2 => __( 'tuesday', 'boxtal-woocommerce' ),
				3 => __( 'wednesday', 'boxtal-woocommerce' ),
				4 => __( 'thursday', 'boxtal-woocommerce' ),
				5 => __( 'friday', 'boxtal-woocommerce' ),
				6 => __( 'saturday', 'boxtal-woocommerce' ),
				7 => __( 'sunday', 'boxtal-woocommerce' ),
			),
		);
		wp_enqueue_script( 'bw_gmap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBLvWeSENu0h4lDozEYIOaAbMbgVtS9EWI' );
		wp_enqueue_script( 'bw_shipping', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/parcel-point.min.js', array( 'bw_gmap' ), $this->plugin_version );
		wp_localize_script( 'bw_shipping', 'translations', $translations );
		$ajax_nonce = wp_create_nonce( 'boxtale_woocommerce' );
		wp_localize_script( 'bw_shipping', 'ajaxNonce', $ajax_nonce );
		wp_localize_script( 'bw_shipping', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'bw_shipping', 'imgDir', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/img/' );
		wp_localize_script( 'bw_shipping', 'googleKey', 'AIzaSyBLvWeSENu0h4lDozEYIOaAbMbgVtS9EWI' );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @void
	 */
	public function load_admin_scripts() {
		wp_enqueue_script( 'bw_polyfills', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/polyfills.min.js', array(), $this->plugin_version );
		wp_enqueue_script( 'bw_component', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/component.min.js', array(), $this->plugin_version );
		wp_localize_script( 'bw_component', 'ajax_nonce', $this->ajax_nonce );
	}

	/**
	 * Enqueue notices scripts
	 *
	 * @void
	 */
	public function notices_scripts() {
		if ( Notices::has_notices() ) {
			wp_enqueue_script( 'bw_notices', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/notices.min.js', array(), $this->plugin_version );
			wp_localize_script( 'bw_notices', 'ajax_nonce', $this->ajax_nonce );
		}
	}

	/**
	 * Enqueue shipping settings scripts
	 *
	 * @param string $hook hook name.
	 * @void
	 */
	public function shipping_settings_scripts( $hook ) {
        // phpcs:ignore
		$current_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? urldecode( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : '';
		if ( 'woocommerce_page_wc-settings' === $hook && 'shipping' === $current_tab ) {
			wp_enqueue_script( 'bw_shipping_settings', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/shipping-settings.min.js', array(), $this->plugin_version );
		}
	}
}
