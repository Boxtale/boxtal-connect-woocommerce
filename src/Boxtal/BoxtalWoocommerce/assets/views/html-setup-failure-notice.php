<?php
/**
 * Setup failure notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-notice bw-warning">
	<?php esc_html_e( 'There was a problem initializing the Boxtal WooCommerce plugin. You should deactivate it and contact our suppor team.', 'boxtal-woocommerce' ); ?>
</div>
