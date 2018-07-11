<?php
/**
 * Contains code for the tracking controller class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Tracking
 */

namespace Boxtal\BoxtalWoocommerce\Tracking;

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
		add_action( 'wp_ajax_get_order_tracking', array( $this, 'get_order_tracking_callback' ) );
		add_action( 'wp_ajax_nopriv_get_order_tracking', array( $this, 'get_order_tracking_callback' ) );
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
	 * Get order tracking callback.
	 *
	 * @void
	 */
	public function get_order_tracking_callback() {
		header( 'Content-Type: application/json; charset=utf-8' );
        // phpcs:ignore
        if ( ! isset( $_REQUEST['order_id'] ) ) {
			wp_send_json( null );
		}

        // phpcs:ignore
        $order_id = sanitize_text_field( wp_unslash( $_REQUEST['order_id'] ) );
		wp_send_json( $this->get_order_tracking( $order_id ) );
	}

	/**
	 * Get order tracking callback.
	 *
	 * @param string $order_id \WC_Order id.
	 * @return array tracking
	 */
	private function get_order_tracking( $order_id ) {
		$shipments = $this->get_order_shipments( $order_id );

		if ( null !== $shipments && ! empty( $shipments ) ) {
			$tracking = array();
			foreach ( $shipments as $shipment ) {
				$tracking[] = array(
					'reference'       => $shipment['carrier_reference'],
					'tracking_url'       => $shipment['carrier_tracking_url'],
					'tracking_events' => $this->get_carrier_tracking( $shipment['carrier_reference'] ),
				);
			}
			return $tracking;
		}
		return null;
	}

	/**
	 * Get order shipments.
	 *
	 * @param string $order_id \WC_Order id.
	 * @return array $shipment mock tracking
	 */
	private function get_order_shipments( $order_id ) {
	    //phpcs:ignore
		// return get_post_meta($order_id, 'bw_shipments', true);
		return array(
			array(
				'carrier_reference'    => 'FRTXXXX',
				'carrier_tracking_url' => 'http://anyurl',
			),
			array(
				'carrier_reference'    => 'GRVVVV',
				'carrier_tracking_url' => 'http://anyurl',
			),
		);
	}

	/**
	 * Get carrier tracking.
	 *
	 * @param string $carrier_reference carrier reference.
	 * @return array tracking events
	 */
	private function get_carrier_tracking( $carrier_reference ) {
		return array(
			array(
				'date'    => '07-01-2018',
				'message' => 'Le colis est arrivé à destination',
			),
			array(
				'date'    => '06-29-2018',
				'message' => 'Le colis est en cours d\'acheminement',
			),
			array(
				'date'    => '06-28-2018',
				'message' => 'Le colis a été déposé au point relais de destination',
			),
		);
	}
}
