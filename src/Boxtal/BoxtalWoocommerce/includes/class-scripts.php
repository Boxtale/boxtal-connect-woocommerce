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
			'Close map'                     => __( 'Close map', 'boxtal-woocommerce' ),
			'Unable to load parcel points:' => __( 'Unable to load parcel points:', 'boxtal-woocommerce' ),
			'I want this pickup point'      => __( 'I want this pickup point', 'boxtal-woocommerce' ),
			'From %1 to %2'                 => __( 'From %1 to %2', 'boxtal-woocommerce' ),
			' and %1 to %2'                 => __( ' and %1 to %2', 'boxtal-woocommerce' ),
			'day_1'                         => __( 'monday', 'boxtal-woocommerce' ),
			'day_2'                         => __( 'tuesday', 'boxtal-woocommerce' ),
			'day_3'                         => __( 'wednesday', 'boxtal-woocommerce' ),
			'day_4'                         => __( 'thursday', 'boxtal-woocommerce' ),
			'day_5'                         => __( 'friday', 'boxtal-woocommerce' ),
			'day_6'                         => __( 'saturday', 'boxtal-woocommerce' ),
			'day_7'                         => __( 'sunday', 'boxtal-woocommerce' ),
			'Opening hours'                 => __( 'Opening hours', 'boxtal-woocommerce' ),
			'relayName'                     => __( 'Choose this Relay Point', 'boxtal-woocommerce' ),
			'noPP'                          => __( 'Could not find any parcel point for this address', 'boxtal-woocommerce' ),
			'selected'                      => __( 'Selected:', 'boxtal-woocommerce' ),
		);
		wp_enqueue_script( 'bw_gmap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBLvWeSENu0h4lDozEYIOaAbMbgVtS9EWI' );
		wp_enqueue_script( 'bw_shipping', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/parcel-point.min.js', array( 'bw_gmap' ), $this->plugin_version );
		wp_localize_script( 'bw_shipping', 'translations', $translations );
		$ajax_nonce = wp_create_nonce( 'boxtale_woocommerce' );
		wp_localize_script( 'bw_shipping', 'ajax_nonce', $ajax_nonce );
		wp_localize_script( 'bw_shipping', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'bw_shipping', 'imgDir', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/img/' );
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
