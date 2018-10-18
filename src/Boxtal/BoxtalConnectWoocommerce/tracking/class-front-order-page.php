<?php
/**
 * Contains code for the front order page class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Tracking
 */

namespace Boxtal\BoxtalConnectWoocommerce\Tracking;

use Boxtal\BoxtalConnectWoocommerce\Util\Order_Util;


/**
 * Front_Order_Page class.
 *
 * Adds tracking info to order page.
 *
 * @class       Front_Order_Page
 * @package     Boxtal\BoxtalConnectWoocommerce\Tracking
 * @category    Class
 * @author      API Boxtal
 */
class Front_Order_Page {

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
		add_filter( 'woocommerce_order_details_after_order_table', array( $this, 'add_tracking_to_front_order_page' ), 10, 2 );
	}

	/**
	 * Add tracking info to front order page.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @void
	 */
	public function add_tracking_to_front_order_page( $order ) {
		$controller = new Controller(
			array(
				'url'     => $this->plugin_url,
				'version' => $this->plugin_version,
			)
		);
		$controller->tracking_styles();
		$tracking = $controller->get_order_tracking( Order_Util::get_id( $order ) );
		//phpcs:ignore
		if ( null !== $tracking && property_exists( $tracking, 'shipmentsTracking' ) && ! empty( $tracking->shipmentsTracking ) ) {
			include realpath( plugin_dir_path( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'html-front-order-tracking.php';
		}
	}
}
