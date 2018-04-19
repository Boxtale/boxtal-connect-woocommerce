<?php
/**
 * Contains code for the setup wizard class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Activation
 */

namespace Boxtal\BoxtalWoocommerce\Activation;

use Boxtal\BoxtalWoocommerce\Admin\Notices;

/**
 * Setup_Wizard class.
 *
 * Display setupe wizard if needed.
 *
 * @class       Setup_Wizard
 * @package     Boxtal\BoxtalWoocommerce\Activation
 * @category    Class
 * @author      API Boxtal
 */
class Setup_Wizard {

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
		if ( false === get_option( 'BW_PLUGIN_SETUP' ) ) {
			Notices::add_notice( 'setup-wizard' );
		}
	}
}
