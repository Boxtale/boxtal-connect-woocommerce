<?php
/**
 * Contains code for the setup wizard class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Init
 */

namespace Boxtal\BoxtalWoocommerce\Init;

use Boxtal\BoxtalWoocommerce\Notice\Notice_Controller;
use Boxtal\BoxtalWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalWoocommerce\Util\Configuration_Util;

/**
 * Setup_Wizard class.
 *
 * Display setup wizard if needed.
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
		if ( Auth_Util::is_plugin_paired() ) {
			if ( Notice_Controller::has_notice( Notice_Controller::$setup_wizard ) ) {
				Notice_Controller::remove_notice( Notice_Controller::$setup_wizard );
			}
			if ( ! Configuration_Util::has_configuration() ) {
				Notice_Controller::add_notice( Notice_Controller::$configuration_failure );
			}
		} elseif ( ! Auth_Util::is_plugin_paired() && ! Notice_Controller::has_notice( Notice_Controller::$setup_wizard ) ) {
			Notice_Controller::add_notice( Notice_Controller::$setup_wizard );
		}
	}
}
