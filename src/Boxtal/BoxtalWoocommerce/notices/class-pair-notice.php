<?php
/**
 * Contains code for the pair notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Notices
 */

namespace Boxtal\BoxtalWoocommerce\Notices;

use Boxtal\BoxtalWoocommerce\Abstracts\Notice;

/**
 * Pair notice class.
 *
 * Pair notice enables pairing with boxtal.
 *
 * @class       Pair_Notice
 * @package     Boxtal\BoxtalWoocommerce\Notices
 * @category    Class
 * @author      API Boxtal
 */
class Pair_Notice extends Notice {

	/**
	 * Construct function.
	 *
	 * @param string $key key for notice.
	 * @void
	 */
	public function __construct( $key ) {
		parent::__construct( $key );
		$this->type         = 'pair';
		$this->autodestruct = false;
	}
}
