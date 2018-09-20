<?php
/**
 * Setup wizard notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-notice bw-info">
	<p><?php esc_html_e( 'Run the setup wizard to connect your shop to Boxtal.', 'boxtal-woocommerce' ); ?></p>
	<p>
		<a href="<?php echo esc_url( $notice->onboarding_link ); ?>" target="_blank" class="button-primary">
			<?php esc_html_e( 'Connect my shop', 'boxtal-woocommerce' ); ?>
		</a>
	</p>
</div>
