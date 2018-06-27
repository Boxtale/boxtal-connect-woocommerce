<?php
/**
 * Pairing failure notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-notice bw-warning">
	<?php esc_html_e( 'Pairing with Boxtal is not complete. Please check your Boxtal Woocommerce connector in your boxtal account for a more complete diagnostic.', 'boxtal-woocommerce' ); ?>
</div>
