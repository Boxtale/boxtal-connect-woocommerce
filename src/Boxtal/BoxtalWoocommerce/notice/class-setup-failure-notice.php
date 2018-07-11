<?php
/**
 * Contains code for the setup failure notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Notice
 */

namespace Boxtal\BoxtalWoocommerce\Notice;

use Boxtal\BoxtalWoocommerce\Util\Customer_Util;

/**
 * Setup failure notice class.
 *
 * Setup failure notice used to display setup error.
 *
 * @class       Setup_Failure_Notice
 * @package     Boxtal\BoxtalWoocommerce\Notice
 * @category    Class
 * @author      API Boxtal
 */
class Setup_Failure_Notice extends Abstract_Notice {

	/**
	 * Construct function.
	 *
	 * @param string $key key for notice.
	 * @void
	 */
	public function __construct( $key ) {
		parent::__construct( $key );
		$this->type         = 'setup-failure';
		$this->autodestruct = false;
		$this->template     = 'html-setup-failure-notice';
	}
}
