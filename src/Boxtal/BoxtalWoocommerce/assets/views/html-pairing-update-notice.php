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
    <?php esc_html_e('Security alert: someone is trying to pair your site with Boxtal. Was it you?', 'boxtal-woocommerce' ); ?>
    <?php echo sprintf(__('%s', 'boxtal-woocommerce' ), '<button class="button-secondary bw-pairing-update-validate" bw-pairing-update-validate="1" href="#">' . __('yes', 'boxtal-woocommerce') . '</button>' ); ?>
    <?php echo sprintf(__('%s', 'boxtal-woocommerce' ), '<button class="button-secondary bw-pairing-update-validate" bw-pairing-update-validate="0" href="#">' . __('no', 'boxtal-woocommerce') . '</button>' ); ?>
</div>