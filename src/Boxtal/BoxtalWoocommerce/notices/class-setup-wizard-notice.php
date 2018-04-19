<?php
/**
 * Contains code for the setup wizard notice class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Notices
 */

namespace Boxtal\BoxtalWoocommerce\Notices;

use Boxtal\BoxtalWoocommerce\Abstracts\Notice;

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
	public $base_connect_link = 'http://localhost:4200/app/connect-shop';

	/**
	 * Connect link.
	 *
	 * @var string $connect_link url.
	 */
	public $connect_link;

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
		$this->connect_link = $this->get_connect_url();
	}

	/**
	 * Build connect link.
	 *
	 * @return string connect link
	 */
	private function get_connect_url() {
		$connect_url = $this->base_connect_link;
		$admins      = get_super_admins();
		if ( count( $admins > 0 ) ) {
			$admin_user_login = array_shift( $admins );
			$admin_user       = get_user_by( 'login', $admin_user_login );
			$admin_user_id    = $admin_user->get( 'id' );
			$customer         = new \WC_Customer( $admin_user_id );
			$params           = array(
				'firstName' => $customer->get_first_name(),
				'lastName'  => $customer->get_last_name(),
				'email'     => $customer->get_email(),
				'phone'     => $customer->get_billing_phone(),
				'address'   => trim( $customer->get_billing_address_1() . ' ' . $customer->get_billing_address_2() ),
				'city'      => $customer->get_billing_city(),
				'postcode'  => $customer->get_billing_postcode(),
				'state'     => $customer->get_billing_state(),
				'country'   => $customer->get_billing_country(),
				'shopUrl'   => get_option( 'siteurl' ),
				'returnUrl' => get_dashboard_url(),
			);
			$connect_url     .= '?' . http_build_query( $params );
		}
		return $connect_url;
	}
}
