<?php
/**
 * Contains code for the setup wizard class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Init
 */

namespace Boxtal\BoxtalWoocommerce\Init;

use Boxtal\BoxtalWoocommerce\Notice\Notice_Controller;

/**
 * Setup_Wizard class.
 *
 * Display setupe wizard if needed.
 *
 * @class       Setup_Wizard
 * @package     Boxtal\BoxtalWoocommerce\Init
 * @category    Class
 * @author      API Boxtal
 */
class Setup_Wizard {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		if ( false === get_option( 'BW_PLUGIN_SETUP' ) ) {
			Notice_Controller::add_notice( 'setup-wizard' );
		}
	}
}
