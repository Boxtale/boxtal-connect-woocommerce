<?php
/**
 * Custom notice rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-notice <?php echo esc_attr( 'bw-' . $notice->status ); ?>">
	<?php echo esc_html( $notice->message ); ?>

	<a class="button-secondary bw-hide-notice" rel="<?php echo esc_attr( $notice->key ); ?>">
		<?php esc_html_e( 'Hide this notice', 'boxtal-connect' ); ?>
	</a>
</div>
