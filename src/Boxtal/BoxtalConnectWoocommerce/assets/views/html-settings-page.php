<?php
/**
 * Settings page rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="wrap">
	<h1>Boxtal Connect</h1>

	<h2><?php esc_html_e( 'Statuses associated to tracking events', 'boxtal-connect' ); ?></h2>
	<div><?php esc_html_e( 'Associate your order statuses to tracking events.', 'boxtal-connect' ); ?></div>

	<form method="post" action="options.php">
		<?php settings_fields( 'boxtal-connect-settings-group' ); ?>
		<?php do_settings_sections( 'boxtal-connect-settings-group' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="order_shipped"><?php esc_html_e( 'Tracking event: order shipped', 'boxtal-connect' ); ?></label>
				</th>
				<td>
					<select name="BW_ORDER_SHIPPED">
						<option value="none"
						<?php
						if ( null === get_option( 'BW_ORDER_SHIPPED' ) ) {
							echo 'selected';}
						?>
						>
							<?php esc_html_e( 'No status associated', 'boxtal-connect' ); ?>
						</option>
						<?php
						foreach ( $order_statuses as $order_status => $translation ) {
							echo '<option value="' . esc_html( $order_status ) . '" ';
							if ( get_option( 'BW_ORDER_SHIPPED' ) === $order_status ) {
								echo 'selected="selected"';
							}
							//phpcs:ignore
							echo '>' . esc_html( __( $translation, 'woocommerce' ) ) . '</option>';
						}
						?>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="order_delivered"><?php esc_html_e( 'Tracking event: order delivered', 'boxtal-connect' ); ?></label>
				</th>
				<td>
					<select name="BW_ORDER_DELIVERED">
						<option value="none"
							<?php
							if ( null === get_option( 'BW_ORDER_DELIVERED' ) ) {
								echo 'selected="selected"';}
							?>
						>
							<?php esc_html_e( 'No status associated', 'boxtal-connect' ); ?>
						</option>
						<?php
						foreach ( $order_statuses as $order_status => $translation ) {
							echo '<option value="' . esc_html( $order_status ) . '" ';
							if ( get_option( 'BW_ORDER_DELIVERED' ) === $order_status ) {
								echo 'selected';
							}
							//phpcs:ignore
							echo '>' . esc_html( __( $translation, 'woocommerce' ) ) . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>
</div>
