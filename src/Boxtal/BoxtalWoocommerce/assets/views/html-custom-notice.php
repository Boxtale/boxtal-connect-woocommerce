<?php
/**
 * Custom notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-notice <?php echo esc_attr( 'bw-' . $notice->status ); ?>">
	<?php echo esc_html( $notice->message ); ?>
	<p>
		<a class="button-secondary bw-hide-notice" rel="<?php echo esc_attr( $notice->key ); ?>">
			<?php esc_html_e( 'Hide this notice', 'boxtal-woocommerce' ); ?>
		</a>
	</p>
</div>
