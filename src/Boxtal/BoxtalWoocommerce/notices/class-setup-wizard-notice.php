<?php
/**
 * Contains code for the setup wizard notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Notices
 */

namespace Boxtal\BoxtalWoocommerce\Notices;

use Boxtal\BoxtalWoocommerce\Abstracts\Notice;
use Boxtal\BoxtalWoocommerce\Helpers\Customer_Helper;

/**
 * Setup wizard notice class.
 *
 * Setup wizard notice used to display setup wizard.
 *
 * @class       Setup_Wizard_Notice
 * @package     Boxtal\BoxtalWoocommerce\Notices
 * @category    Class
 * @author      API Boxtal
 */
class Setup_Wizard_Notice extends Notice {

	/**
	 * Base connect link.
	 *
	 * @var string $base_connect_link url.
	 */
	public $base_connect_link;

	/**
	 * Connect link.
	 *
	 * @var string $connect_link url.
	 */
	public $connect_link;

	/**
	 * Return url.
	 *
	 * @var string $return_url.
	 */
	public $return_url;

	/**
	 * Construct function.
	 *
	 * @param string $key key for notice.
	 * @void
	 */
	public function __construct( $key ) {
		parent::__construct( $key );
		$this->type         = 'setup-wizard';
		$this->autodestruct = false;
		$this->set_base_connect_link( 'http://localhost:4200/app/connect-shop' );
		$this->connect_link = $this->get_connect_url();
		$this->return_url   = get_dashboard_url();
	}

	/**
	 * Build connect link.
	 *
	 * @return string connect link
	 */
	public function get_connect_url() {
		$connect_url = $this->base_connect_link;
		$admins      = get_super_admins();
		if ( count( $admins > 0 ) ) {
			$admin_user_login = array_shift( $admins );
			$admin_user       = get_user_by( 'login', $admin_user_login );
			$admin_user_id    = $admin_user->get( 'ID' );
			$customer         = new \WC_Customer( $admin_user_id );
			$params           = array(
				'firstName'   => Customer_Helper::get_first_name( $customer ),
				'lastName'    => Customer_Helper::get_last_name( $customer ),
				'email'       => Customer_Helper::get_email( $customer ),
				'phone'       => Customer_Helper::get_billing_phone( $customer ),
				'address'     => trim( Customer_Helper::get_billing_address_1( $customer ) . ' ' . Customer_Helper::get_billing_address_2( $customer ) ),
				'city'        => Customer_Helper::get_billing_city( $customer ),
				'postcode'    => Customer_Helper::get_billing_postcode( $customer ),
				'state'       => Customer_Helper::get_billing_state( $customer ),
				'country'     => Customer_Helper::get_billing_country( $customer ),
				'shopUrl'     => get_option( 'siteurl' ),
				'returnUrl'   => $this->return_url,
				'connectType' => 'woocommerce',
				'locale'      => get_locale(),
			);
			$connect_url     .= '?' . http_build_query( $params );
		}
		return $connect_url;
	}

	/**
	 * Build connect link.
	 *
	 * @param string $url base connect link.
	 * @void
	 */
	public function set_base_connect_link( $url ) {
		$this->base_connect_link = $url;
	}

	/**
	 * Set return url.
	 *
	 * @param string $url new return url.
	 * @void
	 */
	public function set_return_url( $url ) {
		$this->return_url = $url;
	}
}
