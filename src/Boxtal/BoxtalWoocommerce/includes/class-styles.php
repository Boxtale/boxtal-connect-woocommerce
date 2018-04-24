<?php
/**
 * Contains code for the styles class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Includes
 */

namespace Boxtal\BoxtalWoocommerce\Includes;

use Boxtal\BoxtalWoocommerce\Admin\Notices;

/**
 * Styles class.
 *
 * Adds styles.
 *
 * @class       Styles
 * @package     Boxtal\BoxtalWoocommerce\Includes
 * @category    Class
 * @author      API Boxtal
 */
class Styles {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'parcel_point_styles' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'notices_styles' ) );
		}
	}

	/**
	 * Enqueue parcel point styles
	 *
	 * @void
	 */
	public function parcel_point_styles() {
		wp_enqueue_style( 'bw_parcel_point', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/parcel-point.css', array(), $this->plugin_version );
	}

	/**
	 * Enqueue admin styles
	 *
	 * @void
	 */
	public function admin_styles() {
		wp_enqueue_style( 'bw_layout', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/layout.css', array(), $this->plugin_version );
		wp_enqueue_style( 'bw_component', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/component.css', array(), $this->plugin_version );
	}

	/**
	 * Enqueue notices styles
	 *
	 * @void
	 */
	public function notices_styles() {
		if ( Notices::has_notices() ) {
			wp_enqueue_style( 'bw_notices', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/notices.css', array(), $this->plugin_version );
		}
	}
}
