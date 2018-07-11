<?php
/**
 * Contains code for the front order page class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Tracking
 */

namespace Boxtal\BoxtalWoocommerce\Tracking;

use Boxtal\BoxtalWoocommerce\Rest_Controller\Order;
use Boxtal\BoxtalWoocommerce\Util\Misc_Util;
use Boxtal\BoxtalWoocommerce\Util\Order_Util;


/**
 * Front_Order_Page class.
 *
 * Adds tracking info to order page.
 *
 * @class       Front_Order_Page
 * @package     Boxtal\BoxtalWoocommerce\Tracking
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
		add_action( 'admin_enqueue_scripts', array( $this, 'tracking_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tracking_styles' ) );
		add_filter( 'woocommerce_order_details_after_order_table', array( $this, 'add_tracking_to_order_page' ), 10, 2 );
	}

	/**
	 * Enqueue tracking scripts
	 *
	 * @param string $order_id order id.
	 * @void
	 */
	public function tracking_scripts( $order_id = null ) {
		wp_enqueue_script( 'bw_front_tracking', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/tracking.min.js', array(), $this->plugin_version );
		if ( null !== $order_id ) {
			wp_localize_script( 'bw_front_tracking', 'orderId', '' . $order_id );
		} else {
			$order = Order_Util::admin_get_order();
			wp_localize_script( 'bw_front_tracking', 'orderId', Order_Util::get_id( $order ) );
		}
		wp_localize_script( 'bw_front_tracking', 'ajaxurl', admin_url( 'admin-ajax.php' ) );

		$translations = array(
			'order_sent_in_1_shipment'  => __( 'Your order has been sent in 1 shipment.', 'boxtal-woocommerce' ),
			/* translators: 1) int number of shipments */
			'order_sent_in_n_shipments' => __( 'Your order has been sent in %s shipments.', 'boxtal-woocommerce' ),
			'shipment_ref' => __( 'Shipment reference %s', 'boxtal-woocommerce' ),
			'no_tracking_event_for_shipment' => __( 'No tracking event for this shipment yet.', 'boxtal-woocommerce' ),
		);
		wp_localize_script( 'bw_front_tracking', 'translations', $translations );
	}

	/**
	 * Enqueue tracking styles
	 *
	 * @void
	 */
	public function tracking_styles() {
		wp_enqueue_style( 'bw_front_tracking', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/tracking.css', array(), $this->plugin_version );
	}

	/**
	 * Add tracking info to front order page.
	 *
	 * @param \WC_Order $order woocommerce order.
	 * @void
	 */
	public function add_tracking_to_order_page( $order ) {
		$this->tracking_scripts( Order_Util::get_id( $order ) );
		$this->tracking_styles();
		include realpath( plugin_dir_path( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'html-order-tracking.php';
	}
}
