<?php
use Boxtal\BoxtalWoocommerce\Boxtal_Woocommerce;

/**
 * Bootstrap for unit tests.
 *
 * Enables autoload and php version compatibility
 *
 * @package Boxtal\Test
 */

class BW_Unit_Tests_Bootstrap {

    /** @var BW_Unit_Tests_Bootstrap instance */
    protected static $instance = null;

    /** @var string directory where wordpress-tests-lib is installed */
    public $wp_tests_dir;

    /** @var string testing directory */
    public $tests_dir;

    /** @var string plugin directory */
    public $plugin_dir;

    /**
     * Setup the unit testing environment.
     */
    public function __construct() {

        // phpcs:disable WordPress.PHP.DiscouragedPHPFunctions, WordPress.PHP.DevelopmentFunctions
        ini_set( 'display_errors', 'on' );
        error_reporting( E_ALL );
        // phpcs:enable WordPress.PHP.DiscouragedPHPFunctions, WordPress.PHP.DevelopmentFunctions

        // Ensure server variable is set for WP email functions.
        // phpcs:disable WordPress.VIP.SuperGlobalInputUsage.AccessDetected
        if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
            $_SERVER['SERVER_NAME'] = 'localhost';
        }
        // phpcs:enable WordPress.VIP.SuperGlobalInputUsage.AccessDetected

        $this->tests_dir    = __DIR__;
        $this->plugin_dir   = dirname( $this->tests_dir );
        $this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib';

        // load test function so tests_add_filter() is available
        require_once $this->wp_tests_dir . '/includes/functions.php';

        // load BW
        tests_add_filter( 'muplugins_loaded', array( $this, 'load_bw' ) );

        // install BW
        tests_add_filter( 'setup_theme', array( $this, 'install_bw' ) );

        // load the WP testing environment
        require_once $this->wp_tests_dir . '/includes/bootstrap.php';

        // load WC testing framework
        $this->includes();
    }

    /**
     * Load Boxtal WooCommerce.
     */
    public function load_bw() {
        require_once $this->plugin_dir . '/class-boxtal-woocommerce.php';
    }

    /**
     * Install Boxtal WooCommerce after the test environment and BW have been loaded.
     */
    public function install_bw() {

        Boxtal_Woocommerce::activate_simple();

        echo esc_html( 'Installing Boxtal WooCommerce...' . PHP_EOL );
    }

    /**
     * Load BW-specific test cases and factories.
     */
    public function includes() {
        // test cases
        require_once $this->tests_dir . '/framework/class-bw-unit-test-case.php';
    }

    /**
     * Get the single class instance.
     *
     * @return BW_Unit_Tests_Bootstrap
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

BW_Unit_Tests_Bootstrap::instance();