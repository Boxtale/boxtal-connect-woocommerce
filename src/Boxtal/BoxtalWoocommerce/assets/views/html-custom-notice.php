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
</div>
