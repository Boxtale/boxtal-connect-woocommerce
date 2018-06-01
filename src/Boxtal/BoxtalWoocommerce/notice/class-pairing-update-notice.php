<?php
/**
 * Contains code for the pairing update notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Notice
 */

namespace Boxtal\BoxtalWoocommerce\Notice;

/**
 * Pairing update notice class.
 *
 * Enables to input pairing code notice.
 *
 * @class       Pairing_Update_Notice
 * @package     Boxtal\BoxtalWoocommerce\Notice
 * @category    Class
 * @author      API Boxtal
 */
class Pairing_Update_Notice extends Abstract_Notice {

	/**
	 * Construct function.
	 *
	 * @param string $key key for notice.
	 * @void
	 */
	public function __construct( $key ) {
		parent::__construct( $key );
		$this->type         = 'pairing-update';
		$this->autodestruct = false;
	}
}
