<?php
/**
 * Contains code for the scripts class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Includes
 */

namespace Boxtal\BoxtalWoocommerce\Includes;

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
		$this->notices        = $plugin['notices'];
		$this->ajax_nonce     = wp_create_nonce( 'boxtale_woocommerce' );
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );

			if ( $this->notices->has_notices() ) {
				add_action( 'admin_enqueue_scripts', array( &$this, 'notices_scripts' ) );
			}
		}
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @void
	 */
	public function admin_scripts() {
		wp_enqueue_script( 'bw_component', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/component.min.js', array(), $this->plugin_version );
		wp_localize_script( 'bw_component', 'ajax_nonce', $this->ajax_nonce );
	}

	/**
	 * Enqueue notices scripts
	 *
	 * @void
	 */
	public function notices_scripts() {
		wp_enqueue_script( 'bw_notices', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/notices.min.js', array(), $this->plugin_version );
		wp_localize_script( 'bw_notices', 'ajax_nonce', $this->ajax_nonce );
	}
}
