<?php
/**
 * Contains code for the setup wizard notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Notice
 */

namespace Boxtal\BoxtalWoocommerce\Notice;

use Boxtal\BoxtalWoocommerce\Util\Customer_Util;

/**
 * Setup wizard notice class.
 *
 * Setup wizard notice used to display setup wizard.
 *
 * @class       Setup_Wizard_Notice
 * @package     Boxtal\BoxtalWoocommerce\Notice
 * @category    Class
 * @author      API Boxtal
 */
class Setup_Wizard_Notice extends Abstract_Notice {

	/**
	 * Signup link.
	 *
	 * @var string $signup_link url.
	 */
	public $signup_link;

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
		$this->signup_link  = $this->get_connect_url();
		$this->template     = 'html-setup-wizard-notice';
	}

	/**
	 * Build connect link.
	 *
	 * @return string connect link
	 */
	public function get_connect_url() {
		$signup_link = get_option( 'BW_SIGNUP_URL' );
		$admins      = get_super_admins();
		if ( is_array( $admins ) && count( $admins ) > 0 ) {
			$admin_user_login = array_shift( $admins );
			$admin_user       = get_user_by( 'login', $admin_user_login );
			$admin_user_id    = $admin_user->get( 'ID' );
		} else {
			$admin_user_id = 1;
		}

		$customer = new \WC_Customer( $admin_user_id );
		$params   = array(
			'firstName'   => Customer_Util::get_first_name( $customer ),
			'lastName'    => Customer_Util::get_last_name( $customer ),
			'email'       => Customer_Util::get_email( $customer ),
			'phone'       => Customer_Util::get_billing_phone( $customer ),
			'address'     => trim( Customer_Util::get_billing_address_1( $customer ) . ' ' . Customer_Util::get_billing_address_2( $customer ) ),
			'city'        => Customer_Util::get_billing_city( $customer ),
			'postcode'    => Customer_Util::get_billing_postcode( $customer ),
			'state'       => Customer_Util::get_billing_state( $customer ),
			'country'     => Customer_Util::get_billing_country( $customer ),
			'shopUrl'     => get_option( 'siteurl' ),
			'returnUrl'   => get_dashboard_url(),
			'connectType' => 'woocommerce',
		);
		return $signup_link . '?' . http_build_query( $params );
	}
}
