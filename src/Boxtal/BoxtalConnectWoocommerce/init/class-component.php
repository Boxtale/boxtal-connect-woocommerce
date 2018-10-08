<?php
/**
 * Contains code for the component class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Init
 */

namespace Boxtal\BoxtalConnectWoocommerce\Init;

/**
 * Component class.
 *
 * Inits components.
 *
 * @class       Component
 * @package     Boxtal\BoxtalConnectWoocommerce\Init
 * @category    Class
 * @author      API Boxtal
 */
class Component {

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
		add_action( 'admin_enqueue_scripts', array( $this, 'component_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'component_styles' ) );
	}

	/**
	 * Enqueue component scripts
	 *
	 * @void
	 */
	public function component_scripts() {
		wp_enqueue_script( 'bw_components', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/js/component.min.js', array(), $this->plugin_version );
	}

	/**
	 * Enqueue component styles
	 *
	 * @void
	 */
	public function component_styles() {
		wp_enqueue_style( 'bw_components', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/component.css', array(), $this->plugin_version );
	}
}
