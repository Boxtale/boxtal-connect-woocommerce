<?php
/**
 * Contains code for the setup wizard class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Activation
 */

namespace Boxtal\BoxtalWoocommerce\Activation;

use Boxtal\BoxtalWoocommerce\Admin\Notices;

/**
 * Setup_Wizard class.
 *
 * Display setupe wizard if needed.
 *
 * @class       Setup_Wizard
 * @package     Boxtal\BoxtalWoocommerce\Activation
 * @category    Class
 * @author      API Boxtal
 */
class Setup_Wizard {

	/**
	 * Account creation link.
	 *
	 * @var string link to account creation on web app.
	 */
	private $account_creation_link = 'http://localhost:4200/app/creation-compte?returnUrl=app/centrale-expeditions/integrations';

	/**
	 * Connect shop link.
	 *
	 * @var string link to connect shop on web app.
	 */
	private $connect_shop_link = 'http://localhost:4200/app/centrale-expeditions/integrations';

	/**
	 * Construct function.
	 *
	 * @param array $plugin plugin array.
	 * @void
	 */
	public function __construct( $plugin ) {
		$this->plugin_url     = $plugin['url'];
		$this->plugin_version = $plugin['version'];
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		if ( false === get_option( 'BW_PLUGIN_SETUP' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
			Notices::add_notice( 'setup-wizard' );
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'bw-setup', '' );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		// phpcs:ignore
		if ( empty( $_GET['page'] ) || 'bw-setup' !== $_GET['page'] ) {
			return;
		}

		wp_enqueue_style( 'setup_wizard_css', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/setup-wizard.css', array(), $this->plugin_version );

		ob_start();
		include realpath( plugin_dir_path( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'html-setup-wizard.php';
		exit;
	}
}
