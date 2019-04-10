<?php
/**
 * Shipping method rate line rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

use Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="pricing-item">
	<td class="sort"></td>

	<td>
		<input type="text" value="<?php echo isset( $pricing_item['price_from'] ) ? esc_html( $pricing_item['price_from'] ) : null; ?>" name='pricing-items[<?php echo esc_html( $i ); ?>]["price-from"]' class="price-from">
	</td>

	<td>
		<input type="text" value="<?php echo isset( $pricing_item['price_to'] ) ? esc_html( $pricing_item['price_to'] ) : null; ?>" name='pricing-items[<?php echo esc_html( $i ); ?>]["price-to"]' class="price-to">
	</td>

	<td>
		<input type="text" value="<?php echo isset( $pricing_item['weight_from'] ) ? esc_html( $pricing_item['weight_from'] ) : null; ?>" name='pricing-items[<?php echo esc_html( $i ); ?>]["weight-from"]' class="weight-from">
	</td>

	<td>
		<input type="text" value="<?php echo isset( $pricing_item['weight_to'] ) ? esc_html( $pricing_item['weight_to'] ) : null; ?>" name='pricing-items[<?php echo esc_html( $i ); ?>]["weight-to"]' class="weight-to">
	</td>

	<td class="select">
		<select name='pricing-items[<?php echo esc_html( $i ); ?>]["shipping-class"][]' multiple="multiple" class="bw-tail-select shipping-class">

			<?php
				$selected = isset( $pricing_item['shipping_class'] ) ? $pricing_item['shipping_class'] : false;
			foreach ( $shipping_classes as $key => $name ) {
				echo '<option value="' . esc_html( $key ) . '" ';
				if ( ( is_array( $selected ) && in_array( $key, $selected, true ) ) || false === $selected ) {
					echo 'selected';
				}
				echo '>' . esc_html( $name ) . '</option>';
			}
			?>
		</select>
	</td>

	<td class="select">
		<select name='pricing-items[<?php echo esc_html( $i ); ?>]["parcel-point-network"][]' multiple="multiple" class="bw-tail-select parcel-point-network">
			<?php
				$selected = isset( $pricing_item['parcel_point_network'] ) ? $pricing_item['parcel_point_network'] : null;
			foreach ( $parcel_point_networks as $network => $name_array ) {
				echo '<option value="' . esc_html( $network ) . '" ';
				if ( ( is_array( $selected ) && in_array( $network, $selected, true ) ) ) {
					echo 'selected';
				}
				echo '>' . esc_html( implode( ', ', $name_array ) ) . '</option>';
			}
			?>
		</select>
	</td>

	<td class="select">
		<select name='pricing-items[<?php echo esc_html( $i ); ?>]["pricing"]' class="bw-tail-select pricing">
			<?php
				$default_value = isset( $pricing_item['pricing'] ) ? $pricing_item['pricing'] : Controller::$rate;
			?>
			<option value="<?php echo esc_html( Controller::$rate ); ?>"
			<?php
			if ( Controller::$rate === $default_value ) {
				echo 'selected';}
			?>
			><?php esc_html_e( 'Flat rate', 'boxtal-connect' ); ?></option>
			<option value="<?php echo esc_html( Controller::$free ); ?>"
			<?php
			if ( Controller::$free === $default_value ) {
				echo 'selected';}
			?>
			><?php esc_html_e( 'Free', 'boxtal-connect' ); ?></option>
			<option value="<?php echo esc_html( Controller::$deactivated ); ?>"
			<?php
			if ( Controller::$deactivated === $default_value ) {
				echo 'selected';}
			?>
			><?php esc_html_e( 'Deactivated', 'boxtal-connect' ); ?></option>
		</select>
	</td>

	<td class="flat-rate">
		<input type="text" id="flat-rate-<?php echo esc_html( $i ); ?>" value="<?php echo isset( $pricing_item['flat_rate'] ) ? esc_html( $pricing_item['flat_rate'] ) : null; ?>" name='pricing-items[<?php echo esc_html( $i ); ?>]["flat-rate"]'
		<?php
		if ( ! isset( $pricing_item['pricing'] ) || ( isset( $pricing_item['pricing'] ) && Controller::$rate !== $pricing_item['pricing'] ) ) {
			echo 'disabled';
		}
		?>
		class="flat-rate">
	</td>
</tr>
