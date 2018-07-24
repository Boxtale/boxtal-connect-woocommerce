<?php
/**
 * Environment warning notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-notice bw-warning">
	<?php echo esc_html( $notice->message ); ?>
</div>
