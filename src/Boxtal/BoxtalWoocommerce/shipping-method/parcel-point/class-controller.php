<?php
/**
 * Contains code for the parcel point controller class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point;

use Boxtal\BoxtalPhp\ApiClient;
use Boxtal\BoxtalWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalWoocommerce\Util\Customer_Util;
use Boxtal\BoxtalWoocommerce\Util\Misc_Util;
use Boxtal\BoxtalWoocommerce\Util\Shipping_Rate_Util;

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
		add_action( 'woocommerce_after_shipping_calculator', array( $this, 'parcel_point_scripts' ) );
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
		$token = Auth_Util::get_maps_token();
		if ( null !== $token ) {
			return str_replace( '${access_token}', $token, get_option( 'BW_MAP_BOOTSTRAP_URL' ) );
		}
		return null;
	}

	/**
	 * Enqueue pickup point script
	 *
	 * @void
	 */
	public function parcel_point_scripts() {
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
				'selectedParcelPoint' => __( 'Your parcel point:', 'boxtal-woocommerce' ),
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
		wp_enqueue_script( 'bw_mapbox_gl', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/mapbox-gl.min.js', array(), $this->plugin_version );
		wp_enqueue_script( 'bw_shipping', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/parcel-point.min.js', array( 'bw_mapbox_gl' ), $this->plugin_version );
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
		wp_enqueue_style( 'bw_mapbox_gl', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/mapbox-gl.css', array(), $this->plugin_version );
		wp_enqueue_style( 'bw_parcel_point', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/parcel-point.css', array(), $this->plugin_version );
	}

	/**
	 * Get parcel point operator options
	 *
	 * @return array operator options
	 */
	public static function get_operator_options() {
		$carriers = get_option( 'BW_PP_OPERATORS' );
		$options  = array();
		foreach ( $carriers as $carrier ) {
			$options[ $carrier->code ] = $carrier->label;
		}
		return $options;
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

		wp_send_json( $this::get_points( $carrier ) );
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
			WC()->session->set(
				'bw_chosen_parcel_point_' . Shipping_Rate_Util::get_clean_id( $carrier ), (object) [
					'operator' => $operator,
					'code'     => $code,
					'label'    => $label,
				]
			);
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
	public static function get_recipient_address() {
	    $customer = Customer_Util::get_customer();
		return array(
			'street'   => trim( Customer_Util::get_shipping_address_1($customer) . ' ' . Customer_Util::get_shipping_address_2($customer) ),
			'city'     => trim( Customer_Util::get_shipping_city($customer) ),
			'postcode' => trim( Customer_Util::get_shipping_postcode($customer) ),
			'country'  => strtolower( Customer_Util::get_shipping_country($customer) ),
		);
	}

	/**
	 * Get parcel points.
	 *
	 * @param array             $address recipient address.
	 * @param \WC_Shipping_Rate $method shipping rate.
	 * @return boolean
	 */
	public static function init_points( $address, $method ) {
		if ( WC()->session ) {
			WC()->session->set( 'bw_parcel_points_' . Shipping_Rate_Util::get_clean_id( $method->id ), null );
		} else {
			return false;
		}

		$settings = Shipping_Rate_Util::get_settings( $method );
		if ( ! is_array( $settings ) ) {
			return false;
		}

		$operators = Misc_Util::get_active_parcel_point_operators( $settings );
		if ( empty( $operators ) ) {
			return false;
		}

		$lib      = new ApiClient( Auth_Util::get_access_key(), Auth_Util::get_secret_key() );
		$response = $lib->getParcelPoints( $address, $operators );

		if ( ! $response->isError() && property_exists( $response->response, 'parcelPoints' ) && is_array( $response->response->parcelPoints ) && count( $response->response->parcelPoints ) > 0 ) {
			WC()->session->set( 'bw_parcel_points_' . Shipping_Rate_Util::get_clean_id( $method->id ), $response->response );
			return true;
		}
		return false;
	}

	/**
	 * Get closest parcel point.
	 *
	 * @param string $id shipping rate id.
	 * @return mixed
	 */
	public static function get_closest_point( $id ) {
		if ( WC()->session ) {
			$parcel_points = WC()->session->get( 'bw_parcel_points_' . Shipping_Rate_Util::get_clean_id( $id ), null );
            //phpcs:ignore
			if ( property_exists( $parcel_points, 'parcelPoints' ) && is_array( $parcel_points->parcelPoints ) && count( $parcel_points->parcelPoints ) > 0 ) {
                //phpcs:ignore
			    return $parcel_points->parcelPoints[0];
			}
		}
		return null;
	}

	/**
	 * Get chosen parcel point.
	 *
	 * @param string $id shipping rate id.
	 * @return mixed
	 */
	public static function get_chosen_point( $id ) {
		if ( WC()->session ) {
			return WC()->session->get( 'bw_chosen_parcel_point_' . Shipping_Rate_Util::get_clean_id( $id ), null );
		}
		return null;
	}

	/**
	 * Reset chosen parcel point.
	 *
	 * @void
	 */
	public static function reset_chosen_points() {
		if ( WC()->session ) {
			foreach ( WC()->session->get_session_data() as $key => $value ) {
				if ( -1 !== strpos( 'bw_chosen_parcel_point_', $key ) ) {
					WC()->session->set( $key, null );
				}
			}
		}
	}

	/**
	 * Get parcel points.
	 *
	 * @param string $id shipping rate id.
	 * @return mixed
	 */
	public static function get_points( $id ) {
		if ( WC()->session ) {
			return WC()->session->get( 'bw_parcel_points_' . Shipping_Rate_Util::get_clean_id( $id ), null );
		}
		return null;
	}
}
