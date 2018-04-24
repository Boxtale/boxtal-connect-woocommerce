<?php
/**
 * Map container html
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="bw-map">
	<div id="bw-map-inner">
		<div class="bw-close" title="<?php esc_html_e( 'Close map', 'boxtal-woocommerce' ); ?>"></div>
		<div id="bw-map-container">
			<div id="bw-map-canvas"></div>
		</div>
		<div id="bw-pp-container"></div>
	</div>
</div>
