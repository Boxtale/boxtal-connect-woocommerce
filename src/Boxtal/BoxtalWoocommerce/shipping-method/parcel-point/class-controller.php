<?php
/**
 * Contains code for the parcel point controller class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point;

use Boxtal\BoxtalPhp\ApiClient;
use Boxtal\BoxtalPhp\ApiResponse;
use Boxtal\BoxtalPhp\RestClient;
use Boxtal\BoxtalWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalWoocommerce\Util\Misc_Util;

/**
 * Controller class.
 *
 * Handles setter and getter for parcel points.
 *
 * @class       Controller
 * @package     Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point
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
		$this->map_url        = null;
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'woocommerce_after_checkout_form', array( $this, 'parcel_point_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'parcel_point_styles' ) );
		add_action( 'wp_ajax_get_points', array( $this, 'get_points_callback' ) );
		add_action( 'wp_ajax_nopriv_get_points', array( $this, 'get_points_callback' ) );
		add_action( 'wp_ajax_set_point', array( $this, 'set_point_callback' ) );
		add_action( 'wp_ajax_nopriv_set_point', array( $this, 'set_point_callback' ) );
		add_action( 'wp_ajax_get_recipient_address', array( $this, 'get_recipient_address_callback' ) );
		add_action( 'wp_ajax_nopriv_get_recipient_address', array( $this, 'get_recipient_address_callback' ) );
	}

	/**
	 * Get map url.
	 *
	 * @void
	 */
	public function get_map_url() {
		return get_option( 'BW_MAP_URL', '' );
	}

	/**
	 * Enqueue pickup point script
	 *
	 * @void
	 */
	public function parcel_point_scripts() {
		if ( ! Misc_Util::is_checkout_page() ) {
			return;
		}

		$translations = array(
			'error' => array(
				'carrierNotFound' => __( 'Unable to find carrier', 'boxtal-woocommerce' ),
				'addressNotFound' => __( 'Could not find address', 'boxtal-woocommerce' ),
				'mapServerError'  => __( 'Could not connect to map server', 'boxtal-woocommerce' ),
			),
			'text'  => array(
				'openingHours'        => __( 'Opening hours', 'boxtal-woocommerce' ),
				'chooseParcelPoint'   => __( 'Choose this parcel point', 'boxtal-woocommerce' ),
				'yourAddress'         => __( 'Your address:', 'boxtal-woocommerce' ),
				'closeMap'            => __( 'Close map', 'boxtal-woocommerce' ),
				'selectedParcelPoint' => __( 'Selected:', 'boxtal-woocommerce' ),
			),
			'day'   => array(
				'MONDAY'    => __( 'monday', 'boxtal-woocommerce' ),
				'TUESDAY'   => __( 'tuesday', 'boxtal-woocommerce' ),
				'WEDNESDAY' => __( 'wednesday', 'boxtal-woocommerce' ),
				'THURSDAY'  => __( 'thursday', 'boxtal-woocommerce' ),
				'FRIDAY'    => __( 'friday', 'boxtal-woocommerce' ),
				'SATURDAY'  => __( 'saturday', 'boxtal-woocommerce' ),
				'SUNDAY'    => __( 'sunday', 'boxtal-woocommerce' ),
			),
		);
		wp_enqueue_script( 'bw_leaflet', 'https://unpkg.com/leaflet@1.3.1/dist/leaflet.js' );
		wp_enqueue_script( 'bw_shipping', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/parcel-point.min.js', array( 'bw_leaflet' ), $this->plugin_version );
		wp_localize_script( 'bw_shipping', 'translations', $translations );
		wp_localize_script( 'bw_shipping', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'bw_shipping', 'imgDir', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/img/' );
		wp_localize_script( 'bw_shipping', 'mapUrl', $this->get_map_url() );
	}

	/**
	 * Enqueue parcel point styles
	 *
	 * @void
	 */
	public function parcel_point_styles() {
		wp_enqueue_style( 'bw_leaflet', 'https://unpkg.com/leaflet@1.3.1/dist/leaflet.css' );
		wp_enqueue_style( 'bw_parcel_point', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/parcel-point.css', array(), $this->plugin_version );
	}

	/**
	 * Get parcel points callback.
	 *
	 * @void
	 */
	public function get_points_callback() {
		header( 'Content-Type: application/json; charset=utf-8' );
        // phpcs:ignore
        if ( ! isset( $_REQUEST['carrier'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Unable to find carrier', 'boxtal-woocommerce' ) ) );
		}
        // phpcs:ignore
		$carrier  = sanitize_text_field( wp_unslash( $_REQUEST['carrier'] ) );
		$settings = Misc_Util::get_settings( $carrier );
		if ( ! isset( $settings['bw_parcel_point_operators'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Something is wrong with this shipping method\'s settings', 'boxtal-woocommerce' ) ) );
		}
		if ( empty( $settings['bw_parcel_point_operators'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No relay operators were defined for this shipping method', 'boxtal-woocommerce' ) ) );
		}

		$address  = $this->get_recipient_address();
		$response = $this->get_points( $address, $settings['bw_parcel_point_operators'] );

		if ( $response->isError() ) {
			wp_send_json_error( array( 'message' => __( 'Something went wrong, could not retrieve parcel points', 'boxtal-woocommerce' ) ) );
		}

		if ( ! ( is_object( $response->response ) && property_exists( $response->response, 'parcelPoints' ) && ! empty( $response->response->parcelPoints ) ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not find any parcel point for this address', 'boxtal-woocommerce' ) ) );
		}

		wp_send_json( $response->response );
	}

	/**
	 * Set parcel point callback.
	 *
	 * @void
	 */
	public function set_point_callback() {
		header( 'Content-Type: application/json; charset=utf-8' );
        // phpcs:ignore
        if ( ! isset( $_REQUEST['carrier'], $_REQUEST['operator'], $_REQUEST['code'], $_REQUEST['label'] ) ) {
			wp_send_json_error( array( 'message' => 'could not set point' ) );
		}
        // phpcs:ignore
		$carrier  = sanitize_text_field( wp_unslash( $_REQUEST['carrier'] ) );
        // phpcs:ignore
        $operator = sanitize_text_field( wp_unslash( $_REQUEST['operator'] ) );
        // phpcs:ignore
        $code     = sanitize_text_field( wp_unslash( $_REQUEST['code'] ) );
        // phpcs:ignore
        $label     = sanitize_text_field( wp_unslash( $_REQUEST['label'] ) );
		if ( WC()->session ) {
			WC()->session->set( 'bw_parcel_point_code_' . $carrier, $code );
			WC()->session->set( 'bw_parcel_point_operator_' . $carrier, $operator );
			WC()->session->set( 'bw_parcel_point_name_' . $carrier, $label );
		} else {
			wp_send_json_error( array( 'message' => 'could not set point. Woocommerce sessions are not enabled!' ) );
		}

		wp_send_json( true );
	}

	/**
	 * Get recipient address.
	 *
	 * @return array recipient address
	 */
	public function get_recipient_address() {
		return array(
			'street'   => trim( WC()->customer->get_shipping_address_1() . ' ' . WC()->customer->get_shipping_address_2() ),
			'city'     => trim( WC()->customer->get_shipping_city() ),
			'postcode' => trim( WC()->customer->get_shipping_postcode() ),
			'country'  => strtolower( WC()->customer->get_shipping_country() ),
		);
	}

	/**
	 * Get parcel points.
	 *
	 * @param array $address recipient address.
	 * @param array $operators parcel point operators.
	 * @return ApiResponse $parcel_points mock parcel points
	 */
	private function get_points( $address, $operators ) {
		$lib = new ApiClient( Auth_Util::get_access_key(), Auth_Util::get_secret_key() );
		return $lib->getParcelPoints( $address, $operators );
	}
}
