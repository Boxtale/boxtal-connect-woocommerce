<?php
/**
 * Admin order view modal tracking rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-order-tracking">
	<h2><?php esc_html_e( 'Tracking details', 'boxtal-woocommerce' ); ?></h2>
	<?php
		require 'html-order-tracking.php';
	?>
</div>
