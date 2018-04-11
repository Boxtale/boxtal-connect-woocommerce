<?php
/**
 * Contains code for the abstract notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Abstracts
 */

namespace Boxtal\BoxtalWoocommerce\Abstracts;

use Boxtal\BoxtalWoocommerce\Admin\Notices;

/**
 * Abstract notice class.
 *
 * Base methods for notices.
 *
 * @class       Notice
 * @package     Boxtal\BoxtalWoocommerce\Abstracts
 * @category    Class
 * @author      API Boxtal
 */
abstract class Notice {


	/**
	 * Notice key, used for remove method.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Notice type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Notice autodestruct.
	 *
	 * @var boolean
	 */
	protected $autodestruct;

	/**
	 * Construct function.
	 *
	 * @param string $key key for notice.
	 * @void
	 */
	public function __construct( $key ) {
		$this->key = $key;
	}

	/**
	 * Render notice.
	 *
	 * @void
	 */
	public function render() {
		$notice = $this;
		include realpath( plugin_dir_path( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'html-' . $this->type . '-notice.php';
		if ( $this->autodestruct ) {
			$this->remove();
		}
	}

	/**
	 * Remove notice.
	 *
	 * @void
	 */
	public function remove() {
		Notices::remove_notice( $this->key );
	}
}
