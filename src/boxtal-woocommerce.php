<?php
/**
 * Plugin Name: Boxtal Woocommerce
 * Plugin URI: https://github.com/boxtal/boxtal-woocommerce-poc
 * Description: Manage multiple carriers using one single plugin and reduce your shipping costs without commitments or any contract to sign.
 * Author: API Boxtal
 * Author URI: https://www.boxtal.com
 * Text Domain: boxtal-woocommerce
 * Domain Path: /Boxtal/BoxtalWoocommerce/translation
 * Version: 0.1.0
 * WC requires at least: 3.0.0
 * WC tested up to: 3.4.0
 *
 * @package Boxtal\BoxtalWoocommerce
 */

use Boxtal\BoxtalWoocommerce\Init\Component;
use Boxtal\BoxtalWoocommerce\Init\Environment_Check;
use Boxtal\BoxtalWoocommerce\Init\Setup_Wizard;
use Boxtal\BoxtalWoocommerce\Init\Translation;
use Boxtal\BoxtalWoocommerce\Notice\Notice_Controller;
use Boxtal\BoxtalWoocommerce\Plugin;
use Boxtal\BoxtalWoocommerce\Rest_Controller\Configuration;
use Boxtal\BoxtalWoocommerce\Rest_Controller\Order;
use Boxtal\BoxtalWoocommerce\Rest_Controller\Shop;
use Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point\Checkout;
use Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point\Label_Override;
use Boxtal\BoxtalWoocommerce\Shipping_Method\Settings_Override;
use Boxtal\BoxtalWoocommerce\Tracking\Admin_Order_Page;
use Boxtal\BoxtalWoocommerce\Tracking\Front_Order_Page;
use Boxtal\BoxtalWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalWoocommerce\Util\Environment_Util;

if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

require_once trailingslashit( __DIR__ ) . 'Boxtal/BoxtalWoocommerce/autoloader.php';

add_action( 'plugins_loaded', 'boxtal_woocommerce_init' );
/**
 * Plugin initialization.
 *
 * @void
 */
function boxtal_woocommerce_init() {
	$plugin                      = new Plugin(); // Create container.
	$plugin['path']              = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
	$plugin['url']               = plugin_dir_url( __FILE__ );
	$plugin['version']           = '0.1.0';
	$plugin['min-wc-version']    = '3.0.0';
	$plugin['min-php-version']   = '5.3.0';
	$plugin['translation']       = 'boxtal_woocommerce_init_translation';
	$plugin['check-environment'] = 'boxtal_woocommerce_check_environment';
	$plugin['notice']            = 'boxtal_woocommerce_init_admin_notices';
    //phpcs:ignore
    // $plugin['component']            = 'boxtal_woocommerce_init_admin_components';
	if ( false === Environment_Util::check_errors( $plugin ) ) {
		$plugin['rest-controller-configuration'] = 'boxtal_woocommerce_rest_controller_configuration';
		$plugin['setup-wizard']                  = 'boxtal_woocommerce_setup_wizard';
		$plugin['rest-controller-shop']          = 'boxtal_woocommerce_rest_controller_shop';
		if ( Auth_Util::can_use_plugin() ) {
			$plugin['tracking-controller']               = 'boxtal_woocommerce_tracking_controller';
			$plugin['front-order-page']                  = 'boxtal_woocommerce_front_order_page';
			$plugin['admin-order-page']                  = 'boxtal_woocommerce_admin_order_page';
			$plugin['rest-controller-order']             = 'boxtal_woocommerce_rest_controller_order';
			$plugin['shipping-method-settings-override'] = 'boxtal_woocommerce_shipping_method_settings_override';
			$plugin['parcel-point-label-override']       = 'boxtal_woocommerce_parcel_point_label_override';
			$plugin['parcel-point-controller']           = 'boxtal_woocommerce_parcel_point_controller';
			$plugin['parcel-point-checkout']             = 'boxtal_woocommerce_parcel_point_checkout';
		}
	}
	$plugin->run();
}

/**
 * Initializes translations.
 *
 * @param array $plugin plugin array.
 * @return Translation $object static translation instance.
 */
function boxtal_woocommerce_init_translation( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Translation( $plugin );
	return $object;
}

/**
 * Initializes common admin components.
 *
 * @param array $plugin plugin array.
 * @return Translation $object static translation instance.
 */
function boxtal_woocommerce_init_admin_components( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Component( $plugin );
	return $object;
}


/**
 * Check PHP version, WC version.
 *
 * @param array $plugin plugin array.
 * @return Environment_Check $environment_check static environment check instance.
 */
function boxtal_woocommerce_check_environment( $plugin ) {
	static $environment_check;

	if ( null !== $environment_check ) {
		return $environment_check;
	}

	$environment_check = new Environment_Check( $plugin );
	return $environment_check;
}

/**
 * Get new Configuration instance.
 *
 * @param array $plugin plugin array.
 * @return Configuration $object
 */
function boxtal_woocommerce_rest_controller_configuration( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Configuration( $plugin );
	return $object;
}

/**
 * Runs install.
 *
 * @param array $plugin plugin array.
 * @return Install $object static setup wizard instance.
 */
function boxtal_woocommerce_setup_wizard( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Setup_Wizard( $plugin );
	return $object;
}

/**
 * Get new Order instance.
 *
 * @param array $plugin plugin array.
 * @return Order $object
 */
function boxtal_woocommerce_rest_controller_order( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Order( $plugin );
	return $object;
}

/**
 * Get new Shop instance.
 *
 * @param array $plugin plugin array.
 * @return Shop $object
 */
function boxtal_woocommerce_rest_controller_shop( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Shop( $plugin );
	return $object;
}

/**
 * Return admin notices singleton.
 *
 * @param array $plugin plugin array.
 * @return Notice_Controller $object
 */
function boxtal_woocommerce_init_admin_notices( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Notice_Controller( $plugin );
	return $object;
}

/**
 * Return settings override singleton.
 *
 * @param array $plugin plugin array.
 * @return Settings_Override $object
 */
function boxtal_woocommerce_shipping_method_settings_override( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Settings_Override( $plugin );
	return $object;
}

/**
 * Return label override singleton.
 *
 * @param array $plugin plugin array.
 * @return Label_Override $object
 */
function boxtal_woocommerce_parcel_point_label_override( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Label_Override( $plugin );
	return $object;
}

/**
 * Parcel point controller.
 *
 * @param array $plugin plugin array.
 * @return Controller $object
 */
function boxtal_woocommerce_parcel_point_controller( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point\Controller( $plugin );
	return $object;
}

/**
 * Manage parcel point checkout.
 *
 * @param array $plugin plugin array.
 * @return Checkout $object
 */
function boxtal_woocommerce_parcel_point_checkout( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Checkout( $plugin );
	return $object;
}

/**
 * Tracking controller.
 *
 * @param array $plugin plugin array.
 * @return Controller $object static controller instance.
 */
function boxtal_woocommerce_tracking_controller( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Boxtal\BoxtalWoocommerce\Tracking\Controller( $plugin );
	return $object;
}

/**
 * Front order page.
 *
 * @param array $plugin plugin array.
 * @return Front_Order_Page $object static Front_Order_Page instance.
 */
function boxtal_woocommerce_front_order_page( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Front_Order_Page( $plugin );
	return $object;
}

/**
 * Admin order page.
 *
 * @param array $plugin plugin array.
 * @return Admin_Order_Page $object static Admin_Order_Page instance.
 */
function boxtal_woocommerce_admin_order_page( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Admin_Order_Page( $plugin );
	return $object;
}
