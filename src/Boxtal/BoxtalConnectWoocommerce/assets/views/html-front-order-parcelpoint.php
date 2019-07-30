<?php
/**
 * Front order tracking rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-order-parcelpoint">
	<h2><?php esc_html_e( 'Chosen pickup point', 'boxtal-connect' ); ?></h2>

	<?php
		require 'html-order-parcelpoint.php';
	?>
</div>
