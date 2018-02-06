<?php
/**
 * Autoloader for boxtal woocommerce plugin
 *
 * @package Boxtal\BoxtalWoocommerce
 */

if ( ! class_exists( 'BW_Autoloader' ) ) {
	/**
	 * Generic autoloader for classes named in WordPress coding style.
	 */
	class BW_Autoloader {

		/**
		 * Source directory
		 *
		 * @static
		 * @var string
		 */
		public $src_dir;

		/**
		 * Constructor for autoloader.
		 *
		 * @param string $dir source directory.
		 * @void
		 */
		public function __construct( $dir = '' ) {
			$this->src_dir = $this->get_src_dir();
			if ( ! empty( $dir ) ) {
				$this->dir = $dir;
			}
			spl_autoload_register( array( $this, 'spl_autoload_register' ) );
		}

		/**
		 * Loads classes names.
		 *
		 * @param string $class_name source folder.
		 * @void
		 */
		public function spl_autoload_register( $class_name ) {
			$class_path = $this->dir . '/class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';
			if ( file_exists( $class_path ) ) {
				include $class_path;
			}
		}

		/**
		 * Get source directory.
		 *
		 * @void
		 */
		private function get_src_dir() {
			return dirname( __DIR__ );
		}
	}
}
