<?php
/**
 * Pairing update notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-notice bw-warning">
	<?php esc_html_e( 'Security alert: someone is trying to pair your site with Boxtal. Was it you?', 'boxtal-woocommerce' ); ?>
	<button class="button-secondary bw-pairing-update-validate" bw-pairing-update-validate="1" href="#"><?php esc_html_e( 'yes', 'boxtal-woocommerce' ); ?></button>
	<button class="button-secondary bw-pairing-update-validate" bw-pairing-update-validate="0" href="#"><?php esc_html_e( 'no', 'boxtal-woocommerce' ); ?></button>
</div>
