<?php
/**
 * Contains code for the parcel point controller class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point;

use Boxtal\BoxtalConnectWoocommerce\Util\Configuration_Util;
use Boxtal\BoxtalPhp\ApiClient;
use Boxtal\BoxtalConnectWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Customer_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Misc_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Shipping_Rate_Util;

/**
 * Controller class.
 *
 * Handles setter and getter for parcel points.
 *
 * @class       Controller
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point
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
				'carrierNotFound' => __( 'Unable to find carrier', 'boxtal-connect' ),
				'addressNotFound' => __( 'Could not find address', 'boxtal-connect' ),
				'mapServerError'  => __( 'Could not connect to map server', 'boxtal-connect' ),
			),
			'text'  => array(
				'openingHours'        => __( 'Opening hours', 'boxtal-connect' ),
				'chooseParcelPoint'   => __( 'Choose this parcel point', 'boxtal-connect' ),
				'yourAddress'         => __( 'Your address:', 'boxtal-connect' ),
				'closeMap'            => __( 'Close map', 'boxtal-connect' ),
				'selectedParcelPoint' => __( 'Your parcel point:', 'boxtal-connect' ),
			),
			'day'   => array(
				'MONDAY'    => __( 'monday', 'boxtal-connect' ),
				'TUESDAY'   => __( 'tuesday', 'boxtal-connect' ),
				'WEDNESDAY' => __( 'wednesday', 'boxtal-connect' ),
				'THURSDAY'  => __( 'thursday', 'boxtal-connect' ),
				'FRIDAY'    => __( 'friday', 'boxtal-connect' ),
				'SATURDAY'  => __( 'saturday', 'boxtal-connect' ),
				'SUNDAY'    => __( 'sunday', 'boxtal-connect' ),
			),
		);
		wp_enqueue_script( 'bw_mapbox_gl', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/js/mapbox-gl.min.js', array(), $this->plugin_version );
		wp_enqueue_script( 'bw_shipping', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/js/parcel-point.min.js', array( 'bw_mapbox_gl' ), $this->plugin_version );
		wp_localize_script( 'bw_shipping', 'translations', $translations );
		wp_localize_script( 'bw_shipping', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'bw_shipping', 'imgDir', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/img/' );
		wp_localize_script( 'bw_shipping', 'mapUrl', $this->get_map_url() );
		wp_localize_script( 'bw_shipping', 'mapLogoImageUrl', Configuration_Util::get_map_logo_image_url() );
		wp_localize_script( 'bw_shipping', 'mapLogoHrefUrl', Configuration_Util::get_map_logo_href_url() );
	}

	/**
	 * Enqueue parcel point styles
	 *
	 * @void
	 */
	public function parcel_point_styles() {
		wp_enqueue_style( 'bw_mapbox_gl', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/mapbox-gl.css', array(), $this->plugin_version );
		wp_enqueue_style( 'bw_parcel_point', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/parcel-point.css', array(), $this->plugin_version );
	}

	/**
	 * Get parcel point network options
	 *
	 * @return array network options
	 */
	public static function get_network_options() {
		$networks = get_option( 'BW_PP_NETWORKS' );
		$options  = array();
		foreach ( $networks as $network => $carrier_array ) {
			$options[ $network ] = implode( ', ', $carrier_array );
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
			wp_send_json_error( array( 'message' => __( 'Unable to find carrier', 'boxtal-connect' ) ) );
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
        if ( ! isset( $_REQUEST['carrier'], $_REQUEST['network'], $_REQUEST['code'], $_REQUEST['name'] ) ) {
			wp_send_json_error( array( 'message' => 'could not set point' ) );
		}
        // phpcs:ignore
        $carrier  = sanitize_text_field( wp_unslash( $_REQUEST['carrier'] ) );
        // phpcs:ignore
        $network = sanitize_text_field( wp_unslash( $_REQUEST['network'] ) );
        // phpcs:ignore
        $code     = sanitize_text_field( wp_unslash( $_REQUEST['code'] ) );
        // phpcs:ignore
        $name     = sanitize_text_field( wp_unslash( $_REQUEST['name'] ) );
		if ( WC()->session ) {
			$parcel_point = new \stdClass();
			//phpcs:disable
            $parcel_point->parcelPoint          = new \stdClass();
            $parcel_point->parcelPoint->network = $network;
            $parcel_point->parcelPoint->code    = $code;
            $parcel_point->parcelPoint->name    = $name;
            //phpcs:enable
			WC()->session->set( 'bw_chosen_parcel_point_' . Shipping_Rate_Util::get_clean_id( $carrier ), $parcel_point );
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
			'street'  => trim( Customer_Util::get_shipping_address_1( $customer ) . ' ' . Customer_Util::get_shipping_address_2( $customer ) ),
			'city'    => trim( Customer_Util::get_shipping_city( $customer ) ),
			'zipCode' => trim( Customer_Util::get_shipping_postcode( $customer ) ),
			'country' => strtolower( Customer_Util::get_shipping_country( $customer ) ),
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

		$networks = Misc_Util::get_active_parcel_point_networks( $settings );
		if ( empty( $networks ) ) {
			return false;
		}

		$lib      = new ApiClient( Auth_Util::get_access_key(), Auth_Util::get_secret_key() );
		$response = $lib->getParcelPoints( $address, $networks );

		if ( ! $response->isError() && property_exists( $response->response, 'nearbyParcelPoints' ) && is_array( $response->response->nearbyParcelPoints )
			&& count( $response->response->nearbyParcelPoints ) > 0 ) {
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
			if ( property_exists( $parcel_points, 'nearbyParcelPoints' ) && is_array( $parcel_points->nearbyParcelPoints ) && count( $parcel_points->nearbyParcelPoints ) > 0 ) {
                //phpcs:ignore
			    return $parcel_points->nearbyParcelPoints[0];
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
