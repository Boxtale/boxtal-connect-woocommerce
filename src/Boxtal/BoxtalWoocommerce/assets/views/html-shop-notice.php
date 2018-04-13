<?php
/**
 * Shop notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-notice bw-info">
	<?php
		/* translators: 1) string "here" link */
		$message = sprintf( __( 'Boxtal Woocommerce is ready to pair, click %s to enter your 6-digit code.', 'boxtal-woocommerce' ), '<a href="#" class="bw-modal-trigger" bw-modal-target="bw-shop-notice-modal">' . __( 'here', 'boxtal-woocommerce' ) . '</a>' );
		echo esc_html( $message );
	?>
</div>

<div id="bw-shop-notice-modal" class="bw-modal">
	<div class="bw-modal-content">
		<h2 class="content-horizontal-center"><?php esc_html_e( 'Enter validation code', 'boxtal-woocommerce' ); ?></h2>
		<div class="content-horizontal-center">
		<?php
			$i = 0;
		while ( $i < 6 ) {
			echo esc_html( '<input type="number" name="bw-digit-' . $i . '" min="0" max="9" size="1" maxlength="1" step="1" oninput="this.value=this.value.substring(0,1);" />' );
			$i++;
		}
			?>
		</div>
		<div class="content-horizontal-center bw-modal-actions">
			<div class="button-primary" id="bw-shop-notice-validate"><?php esc_html_e( 'OK', 'boxtal-woocommerce' ); ?></div>
		</div>
	</div>
</div>

