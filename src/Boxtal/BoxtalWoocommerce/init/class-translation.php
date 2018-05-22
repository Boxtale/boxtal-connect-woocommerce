<?php
/**
 * Contains code for the translation class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Init
 */

namespace Boxtal\BoxtalWoocommerce\Init;

/**
 * Translation class.
 *
 * Inits translation for WP < 4.6.
 *
 * @class       Translation
 * @package     Boxtal\BoxtalWoocommerce\Init
 * @category    Class
 * @author      API Boxtal
 */
class Translation {

	/**
	 * Construct function.
	 *
	 * @param array $plugin plugin array.
	 * @void
	 */
	public function __construct( $plugin ) {
		$this->path = $plugin['path'];
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'init', array( $this, 'boxtal_woocommerce_load_textdomain' ) );
	}

	/**
	 * Loads plugin textdomain.
	 *
	 * @void
	 */
	public function boxtal_woocommerce_load_textdomain() {
		$translation_folder_path = plugin_basename( $this->path . DIRECTORY_SEPARATOR . 'Boxtal' . DIRECTORY_SEPARATOR . 'BoxtalWoocommerce' . DIRECTORY_SEPARATOR . 'translation' );
		load_plugin_textdomain( 'boxtal-woocommerce', false, $translation_folder_path );
	}
}
