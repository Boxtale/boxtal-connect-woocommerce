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
	<p><?php esc_html_e( 'Boxtal Woocommerce install is complete. Run the setup wizard to connect your shop.', 'boxtal-woocommerce' ); ?></p>
	<p>
		<a href="<?php echo esc_url( $notice->connect_link ); ?>" class="button-primary">
			<?php esc_html_e( 'Run the Setup Wizard', 'boxtal-woocommerce' ); ?>
		</a>
	</p>
</div>
