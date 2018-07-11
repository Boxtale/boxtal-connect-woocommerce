<?php
/**
 * Order tracking rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-order-tracking">
	<h2><?php esc_html_e( 'Order tracking', 'boxtal-woocommerce' ); ?></h2>

	<div class="bw-loading">
		<div class="bw-loader"></div>
		<p><?php esc_html_e( 'loading tracking info...', 'boxtal-woocommerce' ); ?></p>
	</div>
	<div class="bw-tracking">
		<p><?php esc_html_e( 'No tracking info available yet', 'boxtal-woocommerce' ); ?></p>
	</div>
</div>
