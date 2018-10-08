<?php
/**
 * Configuration failure notice rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-notice bw-warning">
	<?php esc_html_e( 'There was a problem initializing the Boxtal Connect plugin. You should contact our support team.', 'boxtal-connect' ); ?>
</div>
