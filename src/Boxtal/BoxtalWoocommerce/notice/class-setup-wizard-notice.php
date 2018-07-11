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
		$this->signup_link  = $this->get_signup_url();
		$this->template     = 'html-setup-wizard-notice';
	}

	/**
	 * Build signup link.
	 *
	 * @return string signup link
	 */
	public function get_signup_url() {
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
			'email'    => Customer_Util::get_email( $customer ),
			'shopUrl'  => get_option( 'siteurl' ),
			'shopType' => 'woocommerce',
		);
		return $signup_link . '?' . http_build_query( $params );
	}
}
