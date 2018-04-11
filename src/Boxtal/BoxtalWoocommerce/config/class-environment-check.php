<?php
/**
 * Contains code for the environment check class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Config
 */

namespace Boxtal\BoxtalWoocommerce\Config;

use Boxtal\BoxtalWoocommerce\Admin\Notice;

/**
 * Environment check class.
 *
 * Display environment warning if needed.
 *
 * @class       Environment_Check
 * @package     Boxtal\BoxtalWoocommerce\Config
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
		$this->notices         = $plugin['notices'];
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		$this->environment_warning = $this->boxtal_woocommerce_get_environment_warning();

		if ( false !== $this->environment_warning ) {
			add_action( 'admin_init', array( $this, 'boxtal_woocommerce_display_environment_warning' ) );
		}
	}

	/**
	 * Get warning about PHP version, WC version.
	 *
	 * @return string $message
	 */
	public function boxtal_woocommerce_get_environment_warning() {
		if ( version_compare( phpversion(), $this->min_php_version, '<' ) ) {
			/* translators: 1) int version 2) int version */
			$message = __( 'Boxtal - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'boxtal-woocommerce' );

			return sprintf( $message, $this->min_php_version, phpversion() );
		}

		if ( ! defined( 'WC_VERSION' ) ) {
			return __( 'Boxtal requires WooCommerce to be activated to work.', 'boxtal-woocommerce' );
		}

		if ( version_compare( WC_VERSION, $this->min_wc_version, '<' ) ) {
			/* translators: 1) int version 2) int version */
			$message = __( 'Boxtal - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'boxtal-woocommerce' );

			return sprintf( $message, $this->min_wc_version, WC_VERSION );
		}
		return false;
	}

	/**
	 * Add environment warning notice.
	 *
	 * @void
	 */
	public function boxtal_woocommerce_display_environment_warning() {
		$this->notices->add_notice(
			'custom', array(
				'status'  => 'warning',
				'message' => $this->environment_warning,
			)
		);
	}

}
