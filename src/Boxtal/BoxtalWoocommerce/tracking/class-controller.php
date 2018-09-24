<?php
/**
 * Contains code for the tracking controller class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Tracking
 */

namespace Boxtal\BoxtalWoocommerce\Tracking;

use Boxtal\BoxtalPhp\ApiClient;
use Boxtal\BoxtalWoocommerce\Util\Auth_Util;

/**
 * Controller class.
 *
 * Handles tracking hooks and functions.
 *
 * @class       Controller
 * @package     Boxtal\BoxtalWoocommerce\Tracking
 * @category    Class
 * @author      API Boxtal
 */
class Controller {

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
		$this->handle_tracking_event_hook();
	}

	/**
	 * Handle tracking event hook.
	 *
	 * @void
	 */
	private function handle_tracking_event_hook() {
		$tracking_event = get_option( 'BW_TRACKING_EVENT', array() );
		update_option( 'BW_TRACKING_EVENT', array() ); // remove event in case some buggy code is hooked.

		if ( empty( $tracking_event ) || ! isset( $tracking_event['order_id'], $tracking_event['date'], $tracking_event['code'] ) ) {
			return;
		}

		$order_id            = $tracking_event['order_id'];
		$carrier_reference   = $tracking_event['carrier_reference'];
		$tracking_event_date = $tracking_event['date'];
		$tracking_event_code = $tracking_event['code'];

		do_action( 'boxtal_tracking_event', $order_id, $carrier_reference, $tracking_event_date, $tracking_event_code );
	}

	/**
	 * Get order tracking.
	 *
	 * @param string $order_id \WC_Order id.
	 * @return array tracking
	 */
	public function get_order_tracking( $order_id ) {
		$lib      = new ApiClient( Auth_Util::get_access_key(), Auth_Util::get_secret_key() );
		$response = $lib->getOrder( $order_id );
		if ( $response->isError() ) {
			return null;
		}
		return $response->response;
	}

	/**
	 * Enqueue tracking styles
	 *
	 * @void
	 */
	public function tracking_styles() {
		wp_enqueue_style( 'bw_tracking', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/tracking.css', array(), $this->plugin_version );
	}
}
