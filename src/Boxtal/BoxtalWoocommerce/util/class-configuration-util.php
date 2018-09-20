<?php
/**
 * Contains code for the configuration util class.
 *
 * @package     Boxtal\BoxtalWoocommerce\Util
 */

namespace Boxtal\BoxtalWoocommerce\Util;

/**
 * Configuration util class.
 *
 * Helper to manage configuration.
 *
 * @class       Configuration_Util
 * @package     Boxtal\BoxtalWoocommerce\Util
 * @category    Class
 * @author      API Boxtal
 */
class Configuration_Util {

    /**
     * Build onboarding link.
     *
     * @return string onboarding link
     */
    public static function get_onboarding_link() {
        $url    = BW_ONBOARDING_URL;
        $admins = get_super_admins();
        if ( is_array( $admins ) && count( $admins ) > 0 ) {
            $admin_user_login = array_shift( $admins );
            $admin_user       = get_user_by( 'login', $admin_user_login );
            $admin_user_id    = $admin_user->get( 'ID' );
        } else {
            $admin_user_id = 1;
        }

        $customer = new \WC_Customer( $admin_user_id );
        $params   = array(
            'acceptLanguage' => get_locale(),
            'email'          => Customer_Util::get_email( $customer ),
            'shopUrl'        => get_option( 'siteurl' ),
            'shopType'       => 'woocommerce',
        );
        return $url . '?' . http_build_query( $params );
    }

	/**
	 * Has configuration.
	 *
	 * @return boolean
	 */
	public static function has_configuration() {
		return false !== get_option( 'BW_MAP_BOOTSTRAP_URL' ) && false !== get_option( 'BW_MAP_TOKEN_URL' ) && false !== get_option( 'BW_PP_OPERATORS' );
	}

	/**
	 * Delete configuration.
	 *
	 * @void
	 */
	public static function delete_configuration() {
		global $wpdb;

		delete_option( 'BW_ACCESS_KEY' );
		delete_option( 'BW_SECRET_KEY' );
		delete_option( 'BW_MAP_BOOTSTRAP_URL' );
		delete_option( 'BW_MAP_TOKEN_URL' );
		delete_option( 'BW_PP_OPERATORS' );
		delete_option( 'BW_TRACKING_EVENT' );
		delete_option( 'BW_NOTICES' );
		delete_option( 'BW_PAIRING_UPDATE' );
		//phpcs:ignore
		$wpdb->query(
			$wpdb->prepare(
				"
                DELETE FROM $wpdb->options
		        WHERE option_name LIKE %s
		        ",
				'BW_NOTICE_%'
			)
		);
	}
}
