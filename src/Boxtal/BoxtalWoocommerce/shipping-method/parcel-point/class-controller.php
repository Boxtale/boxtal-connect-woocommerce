<?php
/**
 * Contains code for the parcel point controller class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalWoocommerce\Shipping_Method\Parcel_Point;

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
		$this->ajax_nonce     = wp_create_nonce( 'boxtale_woocommerce' );
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
	 * Enqueue pickup point script
	 *
	 * @void
	 */
	public function parcel_point_scripts() {
		if ( ! Misc_Util::is_checkout_url() ) {
			return;
		}

		$translations = array(
			'error' => array(
				'carrierNotFound'     => __( 'Unable to find carrier', 'boxtal-woocommerce' ),
				'googleQuotaExceeded' => __( 'Google maps API quota exceeded', 'boxtal-woocommerce' ),
				'addressNotFound'     => __( 'Could not find address', 'boxtal-woocommerce' ),
			),
			'text'  => array(
				'openingHours'        => __( 'Opening hours', 'boxtal-woocommerce' ),
				'chooseParcelPoint'   => __( 'Choose this Parcel Point', 'boxtal-woocommerce' ),
				'yourAddress'         => __( 'Your address:', 'boxtal-woocommerce' ),
				'closeMap'            => __( 'Close map', 'boxtal-woocommerce' ),
				'selectedParcelPoint' => __( 'Selected:', 'boxtal-woocommerce' ),
			),
			'day'   => array(
				1 => __( 'monday', 'boxtal-woocommerce' ),
				2 => __( 'tuesday', 'boxtal-woocommerce' ),
				3 => __( 'wednesday', 'boxtal-woocommerce' ),
				4 => __( 'thursday', 'boxtal-woocommerce' ),
				5 => __( 'friday', 'boxtal-woocommerce' ),
				6 => __( 'saturday', 'boxtal-woocommerce' ),
				7 => __( 'sunday', 'boxtal-woocommerce' ),
			),
		);
		wp_enqueue_script( 'bw_gmap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyB6bvHDFhRV9PdJuMJhKsby2gmLmQa1R6g' );
		wp_enqueue_script( 'bw_shipping', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/js/parcel-point.min.js', array( 'bw_gmap' ), $this->plugin_version );
		wp_localize_script( 'bw_shipping', 'translations', $translations );
		wp_localize_script( 'bw_shipping', 'ajaxNonce', $this->ajax_nonce );
		wp_localize_script( 'bw_shipping', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'bw_shipping', 'imgDir', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/img/' );
		wp_localize_script( 'bw_shipping', 'googleKey', 'AIzaSyB6bvHDFhRV9PdJuMJhKsby2gmLmQa1R6g' );
	}

	/**
	 * Enqueue parcel point styles
	 *
	 * @void
	 */
	public function parcel_point_styles() {
		wp_enqueue_style( 'bw_parcel_point', $this->plugin_url . 'Boxtal/BoxtalWoocommerce/assets/css/parcel-point.css', array(), $this->plugin_version );
	}

	/**
	 * Get parcel points callback.
	 *
	 * @void
	 */
	public function get_points_callback() {
		check_ajax_referer( 'boxtale_woocommerce', 'security' );
		header( 'Content-Type: application/json; charset=utf-8' );
		if ( ! isset( $_REQUEST['carrier'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Unable to find carrier', 'boxtal-woocommerce' ) ) );
		}
		$carrier  = sanitize_text_field( wp_unslash( $_REQUEST['carrier'] ) );
		$settings = Misc_Util::get_settings( $carrier );
		if ( ! isset( $settings['bw_map_display'] ) || '0' === $settings['bw_map_display'] || ! isset( $settings['bw_parcel_point_operators'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Something is wrong with this carrier\'s settings', 'boxtal-woocommerce' ) ) );
		}
		if ( empty( $settings['bw_parcel_point_operators'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No relay operators were defined for this carrier', 'boxtal-woocommerce' ) ) );
		}

		$mock_parcel_points = $this->get_mock_points();

		if ( empty( $mock_parcel_points ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not find any parcel point for this address', 'boxtal-woocommerce' ) ) );
		}

		wp_send_json( $mock_parcel_points );
	}

	/**
	 * Set parcel point callback.
	 *
	 * @void
	 */
	public function set_point_callback() {
		check_ajax_referer( 'boxtale_woocommerce', 'security' );
		header( 'Content-Type: application/json; charset=utf-8' );
		if ( ! isset( $_REQUEST['carrier'], $_REQUEST['operator'], $_REQUEST['code'], $_REQUEST['name'] ) ) {
			wp_send_json_error( array( 'message' => 'could not set point' ) );
		}
		$carrier  = sanitize_text_field( wp_unslash( $_REQUEST['carrier'] ) );
		$operator = sanitize_text_field( wp_unslash( $_REQUEST['operator'] ) );
		$code     = sanitize_text_field( wp_unslash( $_REQUEST['code'] ) );
		$name     = sanitize_text_field( wp_unslash( $_REQUEST['name'] ) );
		if ( WC()->session ) {
			WC()->session->set( 'bw_parcel_point_code_' . $carrier, $code );
			WC()->session->set( 'bw_parcel_point_operator_' . $carrier, $operator );
			WC()->session->set( 'bw_parcel_point_name_' . $carrier, $name );
		} else {
			wp_send_json_error( array( 'message' => 'could not set point. Woocommerce sessions are not enabled!' ) );
		}

		wp_send_json( true );
	}

	/**
	 * Get recipient address callback.
	 *
	 * @void
	 */
	public function get_recipient_address_callback() {
		check_ajax_referer( 'boxtale_woocommerce', 'security' );
		header( 'Content-Type: application/json; charset=utf-8' );
		$recipient_address = WC()->customer->get_shipping_address_1() . ' ' . WC()->customer->get_shipping_address_2() . ' '
			. WC()->customer->get_shipping_postcode() . ' ' . WC()->customer->get_shipping_city() . ' ' . WC()->customer->get_shipping_country();
		$recipient_address = preg_replace( '/\s+/', ' ', $recipient_address );
		wp_send_json( $recipient_address );
	}

	/**
	 * Get mock parcel points.
	 *
	 * @return array $mock_parcel_points mock parcel points
	 */
	private function get_mock_points() {
		$mock_schedule = array(
			array(
				'weekday'                 => 1,
				'firstPeriodOpeningTime'  => '10:00:00',
				'firstPeriodClosingTime'  => '21:00:00',
				'secondPeriodOpeningTime' => null,
				'secondPeriodClosingTime' => null,
			),
			array(
				'weekday'                 => 2,
				'firstPeriodOpeningTime'  => '10:00:00',
				'firstPeriodClosingTime'  => '21:00:00',
				'secondPeriodOpeningTime' => null,
				'secondPeriodClosingTime' => null,
			),
			array(
				'weekday'                 => 3,
				'firstPeriodOpeningTime'  => '10:00:00',
				'firstPeriodClosingTime'  => '21:00:00',
				'secondPeriodOpeningTime' => null,
				'secondPeriodClosingTime' => null,
			),
			array(
				'weekday'                 => 4,
				'firstPeriodOpeningTime'  => '10:00:00',
				'firstPeriodClosingTime'  => '21:00:00',
				'secondPeriodOpeningTime' => null,
				'secondPeriodClosingTime' => null,
			),
			array(
				'weekday'                 => 5,
				'firstPeriodOpeningTime'  => '10:00:00',
				'firstPeriodClosingTime'  => '21:00:00',
				'secondPeriodOpeningTime' => null,
				'secondPeriodClosingTime' => null,
			),
			array(
				'weekday'                 => 6,
				'firstPeriodOpeningTime'  => '10:00:00',
				'firstPeriodClosingTime'  => '21:00:00',
				'secondPeriodOpeningTime' => '14:00:00',
				'secondPeriodClosingTime' => '17:00:00',
			),
			array(
				'weekday'                 => 7,
				'firstPeriodOpeningTime'  => '10:00:00',
				'firstPeriodClosingTime'  => '12:00:00',
				'secondPeriodOpeningTime' => '14:00:00',
				'secondPeriodClosingTime' => '17:00:00',
			),
		);
		return array(
			array(
				'code'      => '058017',
				'name'      => 'SARL PARADIS',
				'address'   => '75 BOULEVARD MAGENTA',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8754406',
				'longitude' => '02.3564377',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => '077445',
				'name'      => 'GLADYS',
				'address'   => '98 RUE DU FBG POISSONNIERE',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8782653',
				'longitude' => '02.3494753',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => '043290',
				'name'      => 'PRESSING/BLANCHISSERIE',
				'address'   => '3 PASSAGE DES PETITES ECURIES',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8731282',
				'longitude' => '02.3520973',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => '022063',
				'name'      => 'UNIVERS LINE',
				'address'   => '101 RUE LA FAYETTE',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8776200',
				'longitude' => '02.3501408',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => '001107',
				'name'      => '75 MULTIMEDIA',
				'address'   => '29 BOULEVARD DE MAGENTA',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8711706',
				'longitude' => '02.3602504',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => '001228',
				'name'      => 'EUROPE MULTI SERVICES',
				'address'   => '157 FBG SAINT DENIS',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8789755',
				'longitude' => '02.3567096',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => '002939',
				'name'      => 'AMS BATIMENT SERVICES',
				'address'   => '57 RUE DE PARADIS',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8756056',
				'longitude' => '02.3486373',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => '040797',
				'name'      => 'ITELNET',
				'address'   => '70 RUE FAUBOURG POISSONNIERE',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8761143',
				'longitude' => '02.3486736',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => '001765',
				'name'      => 'GSM SOLUTIONS',
				'address'   => '185-187 RUE SAINT MAUR',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8711206',
				'longitude' => '02.3723242',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => '000512',
				'name'      => 'MA TELECOM',
				'address'   => '24 RUE DU BUISSON SAINT LOUIS',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8727068',
				'longitude' => '02.3744086',
				'operator'  => 'MONR',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C1284',
				'name'      => 'MEGNA',
				'address'   => '71 RUE DU FAUBOURG SAINT MARTIN',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8722',
				'longitude' => '2.35724',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C1177',
				'name'      => 'OPTISOINS',
				'address'   => '77 RUE DU FAUBOURG SAINT DENIS',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8731',
				'longitude' => '2.35442',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C1219',
				'name'      => 'KM RALPH BEAUTE',
				'address'   => '7 PASSAGE DU PRADO',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8701',
				'longitude' => '2.35366',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C1186',
				'name'      => 'MINI MARKET',
				'address'   => '101 RUE DU FAUBOURG SAINT MARTIN',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8686',
				'longitude' => '2.35825',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C1045',
				'name'      => 'ALIMENTATION GENERALE',
				'address'   => '7-9 RUE DE LANCRY',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8691',
				'longitude' => '2.36001',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C1119',
				'name'      => 'ARMOZO',
				'address'   => '60 RUE MESLAY',
				'city'      => 'PARIS',
				'zipcode'   => '75003',
				'country'   => 'FR',
				'latitude'  => '48.8686',
				'longitude' => '2.35579',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C1160',
				'name'      => 'INFORMATIQUE',
				'address'   => '34 RUE DU FAUBOURG SAINT MARTIN',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8684',
				'longitude' => '2.35903',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C1111',
				'name'      => 'PRODUITS EXOTIQUES',
				'address'   => '56 RUE NOTRE DAME DE NAZARETH',
				'city'      => 'PARIS',
				'zipcode'   => '75003',
				'country'   => 'FR',
				'latitude'  => '48.8677',
				'longitude' => '2.35726',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C1165',
				'name'      => 'PARADIS INFORMATIQUE',
				'address'   => '75 BOULEVARD DE MAGENTA',
				'city'      => 'PARIS',
				'zipcode'   => '75010',
				'country'   => 'FR',
				'latitude'  => '48.8757',
				'longitude' => '2.35647',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
			array(
				'code'      => 'C10A3',
				'name'      => 'POINT FORT FICHET',
				'address'   => '17 RUE DES FONTAINES DU TEMPLE',
				'city'      => 'PARIS',
				'zipcode'   => '75003',
				'country'   => 'FR',
				'latitude'  => '48.8657',
				'longitude' => '2.35871',
				'operator'  => 'SOGP',
				'schedule'  => $mock_schedule,
			),
		);
	}
}
