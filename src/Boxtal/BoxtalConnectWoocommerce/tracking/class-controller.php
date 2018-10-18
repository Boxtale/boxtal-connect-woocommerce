<?php
/**
 * Contains code for the tracking controller class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Tracking
 */

namespace Boxtal\BoxtalConnectWoocommerce\Tracking;

use Boxtal\BoxtalPhp\ApiClient;
use Boxtal\BoxtalConnectWoocommerce\Util\Auth_Util;

/**
 * Controller class.
 *
 * Handles tracking hooks and functions.
 *
 * @class       Controller
 * @package     Boxtal\BoxtalConnectWoocommerce\Tracking
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
		add_action( 'wp_loaded', array( $this, 'handle_tracking_event_hook' ) );
	}

	/**
	 * Handle tracking event hook.
	 *
	 * @void
	 */
	public function handle_tracking_event_hook() {
		$tracking_events = get_option( 'BW_TRACKING_EVENTS', array() );

		foreach ( $tracking_events as $tracking_event ) {

			if ( empty( $tracking_event ) || ! isset( $tracking_event['order_id'], $tracking_event['date'], $tracking_event['code'], $tracking_event['carrier_reference'] ) ) {
				continue;
			}
			$order_id            = $tracking_event['order_id'];
			$carrier_reference   = $tracking_event['carrier_reference'];
			$tracking_event_date = $tracking_event['date'];
			$tracking_event_code = $tracking_event['code'];

			do_action( 'boxtal_tracking_event', $order_id, $carrier_reference, $tracking_event_date, $tracking_event_code );
		}

		update_option( 'BW_TRACKING_EVENTS', array() ); // remove event in case some buggy code is hooked.
	}

	/**
	 * Get order tracking.
	 *
	 * @param string $order_id \WC_Order id.
	 * @return object tracking
	 */
	public function get_order_tracking( $order_id ) {
	    return json_decode('{
  "reference": "reference order",
  "shipmentsTracking": [
    {
      "reference": "reference shipment",
      "parcelsTracking": [
        {
          "reference": "reference parcel",
          "status": "A",
          "trackingUrl": "http://anyurl",
          "trackingEvents": [
            {
              "date": "1977-04-22T06:00:00Z",
              "message": "message",
              "status": "A"
            }
          ]
        }
      ]
    }
  ]
}');


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
		wp_enqueue_style( 'bw_tracking', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/tracking.css', array(), $this->plugin_version );
	}
}
