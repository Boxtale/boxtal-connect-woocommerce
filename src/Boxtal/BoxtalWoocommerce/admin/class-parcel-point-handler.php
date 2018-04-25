<?php
/**
 * Contains code for the parcel point handler class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Admin
 */

namespace Boxtal\BoxtalWoocommerce\Admin;

use Boxtal\BoxtalWoocommerce\Helpers\Helper_Functions;

/**
 * Parcel_Point_Handler class.
 *
 * Handles setter and getter for parcel points.
 *
 * @class       Parcel_Point_Handler
 * @package     Boxtal\BoxtalWoocommerce\Admin
 * @category    Class
 * @author      API Boxtal
 */
class Parcel_Point_Handler {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'wp_ajax_get_points', array( $this, 'get_points_callback' ) );
		add_action( 'wp_ajax_nopriv_get_points', array( $this, 'get_points_callback' ) );
		add_action( 'wp_ajax_set_point', array( $this, 'set_point_callback' ) );
		add_action( 'wp_ajax_nopriv_set_point', array( $this, 'set_point_callback' ) );
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
			wp_send_json_error( array( 'message' => 'could not figure carrier' ) );
		}
		$carrier  = sanitize_text_field( wp_unslash( $_REQUEST['carrier'] ) );
		$settings = Helper_Functions::get_settings( $carrier );
		if ( ! isset( $settings['bw_tag_category'], $settings['bw_tag_relay_operators'] ) || 'relay' !== $settings['bw_tag_category'] ) {
			wp_send_json_error( array( 'message' => 'something is wrong with this carrier\'s settings' ) );
		}
		$operators = $settings['bw_tag_relay_operators'];
		if ( empty( $operators ) ) {
			wp_send_json_error( array( 'message' => 'no relay operators were defined for this carrier' ) );
		}

		$mock_parcel_points = $this->get_mock_points();

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
			WC()->session->set( 'bw_pickup_point_code_' . $carrier, $code );
			WC()->session->set( 'bw_pickup_point_operator_' . $carrier, $operator );
			WC()->session->set( 'bw_pickup_point_name_' . $carrier, $name );
		} else {
			wp_send_json_error( array( 'message' => 'could not set point. Woocommerce sessions are not enabled!' ) );
		}

		wp_send_json( true );
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
