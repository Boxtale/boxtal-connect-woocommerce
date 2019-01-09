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
	}

	/**
	 * Get order tracking.
	 *
	 * @param string $order_id \WC_Order id.
	 * @return object tracking
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
		wp_enqueue_style( 'bw_tracking', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/tracking.css', array(), $this->plugin_version );
	}
}
