<?php
/**
 * Contains code for the environment check class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Init
 */

namespace Boxtal\BoxtalWoocommerce\Init;

use Boxtal\BoxtalWoocommerce\Notice\Notice_Controller;

/**
 * Environment check class.
 *
 * Display environment warning if needed.
 *
 * @class       Environment_Check
 * @package     Boxtal\BoxtalWoocommerce\Init
 * @category    Class
 * @author      API Boxtal
 */
class Environment_Check {

	/**
	 * Environment warning message.
	 *
	 * @var string.
	 */
	private $environment_warning;

	/**
	 * Construct function.
	 *
	 * @param array $plugin plugin array.
	 * @void
	 */
	public function __construct( $plugin ) {
		$this->min_php_version = $plugin['min-php-version'];
		$this->min_wc_version  = $plugin['min-wc-version'];
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		$this->environment_warning = $this->get_environment_warning();

		if ( false !== $this->environment_warning ) {
			add_action( 'admin_init', array( $this, 'display_environment_warning' ) );
		}
	}

	/**
	 * Get warning about PHP version, WC version.
	 *
	 * @return string $message
	 */
	public function get_environment_warning() {
		if ( version_compare( PHP_VERSION, $this->min_php_version, '<' ) ) {
			/* translators: 1) int version 2) int version */
			$message = __( 'Boxtal Woocommerce - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'boxtal-woocommerce' );

			return sprintf( $message, $this->min_php_version, PHP_VERSION );
		}

		if ( ! defined( 'WC_VERSION' ) ) {
			return __( 'Boxtal Woocommerce requires WooCommerce to be activated to work.', 'boxtal-woocommerce' );
		}

		if ( version_compare( WC_VERSION, $this->min_wc_version, '<' ) ) {
			/* translators: 1) int version 2) int version */
			$message = __( 'Boxtal Woocommerce - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'boxtal-woocommerce' );

			return sprintf( $message, $this->min_wc_version, WC_VERSION );
		}
		return false;
	}

	/**
	 * Add environment warning notice.
	 *
	 * @void
	 */
	public function display_environment_warning() {
		Notice_Controller::add_notice(
			Notice_Controller::$custom, array(
				'status'  => 'warning',
				'message' => $this->environment_warning,
			)
		);
	}

}
