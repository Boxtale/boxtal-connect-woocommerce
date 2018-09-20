<?php
/**
 * Contains code for the setup wizard notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Notice
 */

namespace Boxtal\BoxtalWoocommerce\Notice;

use Boxtal\BoxtalWoocommerce\Util\Configuration_Util;

/**
 * Setup wizard notice class.
 *
 * Setup wizard notice used to display setup wizard.
 *
 * @class       Setup_Wizard_Notice
 * @package     Boxtal\BoxtalWoocommerce\Notice
 * @category    Class
 * @author      API Boxtal
 */
class Setup_Wizard_Notice extends Abstract_Notice {

	/**
	 * Onboarding link.
	 *
	 * @var string $onboarding_link url.
	 */
	public $onboarding_link;

	/**
	 * Construct function.
	 *
	 * @param string $key key for notice.
	 * @void
	 */
	public function __construct( $key ) {
		parent::__construct( $key );
		$this->type            = 'setup-wizard';
		$this->autodestruct    = false;
		$this->onboarding_link = Configuration_Util::get_onboarding_link();
		$this->template        = 'html-setup-wizard-notice';
	}
}
