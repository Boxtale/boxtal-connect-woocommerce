<?php
/**
 * Contains code for the styles class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Includes
 */

namespace Boxtal\BoxtalWoocommerce\Includes;

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
		$this->notices        = $plugin['notices'];
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_styles' ) );

			if ( $this->notices->has_notices() ) {
				add_action( 'admin_enqueue_scripts', array( &$this, 'notices_styles' ) );
			}
		}
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
		wp_enqueue_style( 'bw_notices', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/notices.css', array(), $this->plugin_version );
	}
}
