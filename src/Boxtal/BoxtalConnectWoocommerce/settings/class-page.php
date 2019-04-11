<?php
/**
 * Contains code for the settings page class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Settings
 */

namespace Boxtal\BoxtalConnectWoocommerce\Settings;

use Boxtal\BoxtalConnectWoocommerce\Util\Misc_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Shipping_Method_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Configuration_Util;

/**
 * Settings page class.
 *
 * Manages settings for the Boxtal Connect plugin.
 *
 * @class       Page
 * @package     Boxtal\BoxtalConnectWoocommerce\Settings
 * @category    Class
 * @author      API Boxtal
 */
class Page {

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
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		//phpcs:ignore
		if (isset($_GET['page']) && 'boxtal-connect-settings' === $_GET['page']) {
			add_action( 'admin_enqueue_scripts', array( $this, 'settings_page_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'settings_page_styles' ) );
		}
	}

	/**
	 * Enqueue settings page scripts
	 *
	 * @void
	 */
	public function settings_page_scripts() {
		wp_enqueue_script( 'bw_tail_select', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/js/tail.select-full.min.js', array(), $this->plugin_version );
		wp_enqueue_script( 'bw_settings_page', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/js/settings-page.min.js', array( 'bw_tail_select' ), $this->plugin_version );
		wp_localize_script( 'bw_settings_page', 'bwLocale', substr( get_locale(), 0, 2 ) );
	}

	/**
	 * Enqueue settings page styles
	 *
	 * @void
	 */
	public function settings_page_styles() {
		wp_enqueue_style( 'bw_tail_select', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/tail.select-bootstrap3.css', array(), $this->plugin_version );
		wp_enqueue_style( 'bw_parcel_point', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/settings.css', array(), $this->plugin_version );
	}

	/**
	 * Add settings page.
	 *
	 * @void
	 */
	public function add_menu() {
		add_submenu_page( 'woocommerce', __( 'Boxtal Connect', 'boxtal-connect' ), __( 'Boxtal Connect', 'boxtal-connect' ), 'manage_woocommerce', 'boxtal-connect-settings', array( $this, 'render_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register settings.
	 *
	 * @void
	 */
	public function register_settings() {
		register_setting(
			'boxtal-connect-settings-group',
			'BW_ORDER_SHIPPED',
			array(
				'type'              => 'string',
				'description'       => __( 'Order shipped ', 'boxtal-connect' ),
				'default'           => null,
				'sanitize_callback' => array( $this, 'sanitize_status' ),
			)
		);
		register_setting(
			'boxtal-connect-settings-group',
			'BW_ORDER_DELIVERED',
			array(
				'type'              => 'string',
				'description'       => __( 'Order delivered ', 'boxtal-connect' ),
				'default'           => null,
				'sanitize_callback' => array( $this, 'sanitize_status' ),
			)
		);
	}

	/**
	 * Render settings page.
	 *
	 * @void
	 */
	public function render_page() {
		$order_statuses  = wc_get_order_statuses();
		$help_center_url = Configuration_Util::get_help_center_link();
		include_once dirname( __DIR__ ) . '/assets/views/html-settings-page.php';
	}

	/**
	 * Sanitize status option.
	 *
	 * @param string $input status value.
	 *
	 * @return string
	 */
	public function sanitize_status( $input ) {
		return 'none' === $input ? null : $input;
	}
}
