<?php
/**
 * Contains code for the setup wizard notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Notices
 */

namespace Boxtal\BoxtalWoocommerce\Notices;

use Boxtal\BoxtalWoocommerce\Abstracts\Notice;

/**
 * Setup wizard notice class.
 *
 * Setup wizard notice used to display setup wizard.
 *
 * @class       Setup_Wizard_Notice
 * @package     Boxtal\BoxtalWoocommerce\Notices
 * @category    Class
 * @author      API Boxtal
 */
class Setup_Wizard_Notice extends Notice {

	/**
	 * Construct function.
	 *
	 * @param string $key key for notice.
	 * @void
	 */
	public function __construct( $key ) {
		parent::__construct( $key );
		$this->type         = 'setup-wizard';
		$this->autodestruct = false;
	}
}
