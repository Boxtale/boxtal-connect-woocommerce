<?php
/**
 * Plugin Name: Boxtal Connect
 * Description: Managing your shipments becomes easier with our free plugin Boxtal! Save time and enjoy negotiated rates with 15 carriers: Colissimo, Mondial Relay...
 * Author: API Boxtal
 * Author URI: https://www.boxtal.com
 * Text Domain: boxtal-connect
 * Domain Path: /Boxtal/BoxtalConnectWoocommerce/translation
 * Version: 1.0.2
 * WC requires at least: 2.6.14
 * WC tested up to: 3.4.6
 *
 * @package Boxtal\BoxtalConnectWoocommerce
 */

use Boxtal\BoxtalConnectWoocommerce\Init\Component;
use Boxtal\BoxtalConnectWoocommerce\Init\Environment_Check;
use Boxtal\BoxtalConnectWoocommerce\Init\Setup_Wizard;
use Boxtal\BoxtalConnectWoocommerce\Init\Translation;
use Boxtal\BoxtalConnectWoocommerce\Notice\Notice_Controller;
use Boxtal\BoxtalConnectWoocommerce\Plugin;
use Boxtal\BoxtalConnectWoocommerce\Rest_Controller\Order;
use Boxtal\BoxtalConnectWoocommerce\Rest_Controller\Shop;
use Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point\Checkout;
use Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point\Label_Override;
use Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Settings_Override;
use Boxtal\BoxtalConnectWoocommerce\Tracking\Admin_Order_Page;
use Boxtal\BoxtalConnectWoocommerce\Tracking\Front_Order_Page;
use Boxtal\BoxtalConnectWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Configuration_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Environment_Util;

if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

require_once trailingslashit( __DIR__ ) . 'Boxtal/BoxtalConnectWoocommerce/autoloader.php';

add_action( 'plugins_loaded', 'boxtal_connect_init' );
/**
 * Plugin initialization.
 *
 * @void
 */
function boxtal_connect_init() {

	define( 'BW_ONBOARDING_URL', 'https://www.boxtal.com/onboarding' );

	$plugin                      = new Plugin(); // Create container.
	$plugin['path']              = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
	$plugin['url']               = plugin_dir_url( __FILE__ );
	$plugin['version']           = '1.0.2';
	$plugin['min-wc-version']    = '2.6.14';
	$plugin['min-php-version']   = '5.6.0';
	$plugin['translation']       = 'boxtal_connect_init_translation';
	$plugin['check-environment'] = 'boxtal_connect_check_environment';
	$plugin['notice']            = 'boxtal_connect_init_admin_notices';
    //phpcs:ignore
    // $plugin['component']            = 'boxtal_connect_init_admin_components';
	if ( false === Environment_Util::check_errors( $plugin ) ) {
		$plugin['setup-wizard']         = 'boxtal_connect_setup_wizard';
		$plugin['rest-controller-shop'] = 'boxtal_connect_rest_controller_shop';
		if ( Auth_Util::can_use_plugin() ) {
			$plugin['tracking-controller']               = 'boxtal_connect_tracking_controller';
			$plugin['front-order-page']                  = 'boxtal_connect_front_order_page';
			$plugin['admin-order-page']                  = 'boxtal_connect_admin_order_page';
			$plugin['rest-controller-order']             = 'boxtal_connect_rest_controller_order';
			$plugin['shipping-method-settings-override'] = 'boxtal_connect_shipping_method_settings_override';
			$plugin['parcel-point-label-override']       = 'boxtal_connect_parcel_point_label_override';
			$plugin['parcel-point-controller']           = 'boxtal_connect_parcel_point_controller';
			$plugin['parcel-point-checkout']             = 'boxtal_connect_parcel_point_checkout';
		}
	}
	$plugin->run();
}

register_uninstall_hook( __FILE__, 'boxtal_connect_uninstall_network' );
/**
 * Network uninstall.
 *
 * @param boolean $network_wide whether it is a network wide uninstall or not.
 * @void
 */
function boxtal_connect_uninstall_network( $network_wide ) {
	if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {
		global $wpdb;
		$current_blog = $wpdb->blogid;

		//phpcs:ignore
		$blog_ids = $wpdb->get_col( 'SELECT blog_id FROM ' . $wpdb->blogs );
		foreach ( $blog_ids as $blog_id ) {
            //phpcs:ignore
			switch_to_blog( $blog_id );
			boxtal_connect_uninstall_simple();
		}
        //phpcs:ignore
		switch_to_blog( $current_blog );
	} else {
		boxtal_connect_uninstall_simple();
	}
}

/**
 * Simple uninstall.
 *
 * @void
 */
function boxtal_connect_uninstall_simple() {
	Configuration_Util::delete_configuration();
}

/**
 * Initializes translations.
 *
 * @param array $plugin plugin array.
 * @return Translation $object static translation instance.
 */
function boxtal_connect_init_translation( $plugin ) {
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
function boxtal_connect_init_admin_components( $plugin ) {
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
function boxtal_connect_check_environment( $plugin ) {
	static $environment_check;

	if ( null !== $environment_check ) {
		return $environment_check;
	}

	$environment_check = new Environment_Check( $plugin );
	return $environment_check;
}

/**
 * Runs install.
 *
 * @param array $plugin plugin array.
 * @return Install $object static setup wizard instance.
 */
function boxtal_connect_setup_wizard( $plugin ) {
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
function boxtal_connect_rest_controller_order( $plugin ) {
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
function boxtal_connect_rest_controller_shop( $plugin ) {
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
function boxtal_connect_init_admin_notices( $plugin ) {
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
function boxtal_connect_shipping_method_settings_override( $plugin ) {
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
function boxtal_connect_parcel_point_label_override( $plugin ) {
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
function boxtal_connect_parcel_point_controller( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point\Controller( $plugin );
	return $object;
}

/**
 * Manage parcel point checkout.
 *
 * @param array $plugin plugin array.
 * @return Checkout $object
 */
function boxtal_connect_parcel_point_checkout( $plugin ) {
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
function boxtal_connect_tracking_controller( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Boxtal\BoxtalConnectWoocommerce\Tracking\Controller( $plugin );
	return $object;
}

/**
 * Front order page.
 *
 * @param array $plugin plugin array.
 * @return Front_Order_Page $object static Front_Order_Page instance.
 */
function boxtal_connect_front_order_page( $plugin ) {
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
function boxtal_connect_admin_order_page( $plugin ) {
	static $object;

	if ( null !== $object ) {
		return $object;
	}

	$object = new Admin_Order_Page( $plugin );
	return $object;
}
