<?php
/**
 * Contains code for helper functions class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 */

namespace Boxtal\BoxtalWoocommerce\Helpers;

/**
 * Helper functions class.
 *
 * Generic helper functions.
 *
 * @class       Helper_Functions
 * @package     Boxtal\BoxtalWoocommerce\Helpers
 * @category    Class
 * @author      API Boxtal
 */
class Helper_Functions {
	/**
	 * Returns value if not empty, null otherwise.
	 *
	 * @param mixed $value value to be checked.
	 * @return mixed $value
	 */
	public static function not_empty_or_null( $value ) {
		return '' === $value ? null : $value;
	}
}
