<?php
/**
 * Pairing success notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>


<div class="bw-notice bw-success">
	<?php esc_html_e( 'Congratulations! You\'ve successfully paired your site with Boxtal.', 'boxtal-woocommerce' ); ?>
	<p>
		<a class="button-secondary bw-hide-notice" rel="pairing">
			<?php esc_html_e( 'Hide this notice', 'boxtal-woocommerce' ); ?>
		</a>
	</p>
</div>
