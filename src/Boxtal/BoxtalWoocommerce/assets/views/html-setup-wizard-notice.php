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
	<p><?php esc_html_e( 'Boxtal Woocommerce install is complete. Run the setup wizard to create a Boxtal account and/or connect your shop.', 'boxtal-woocommerce' ); ?></p>
	<p>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=bw-setup' ) ); ?>" class="button-primary">
			<?php esc_html_e( 'Run the Setup Wizard', 'boxtal-woocommerce' ); ?>
		</a>
		<a class="button-secondary bw-hide-notice" rel="setup-wizard">
			<?php esc_html_e( 'Hide this notice', 'boxtal-woocommerce' ); ?>
		</a>
	</p>
</div>
