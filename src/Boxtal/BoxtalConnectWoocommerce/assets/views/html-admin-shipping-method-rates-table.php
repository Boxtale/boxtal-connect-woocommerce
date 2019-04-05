<?php
/**
 * Shipping method rates table rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

use Boxtal\BoxtalConnectWoocommerce\Util\Misc_Util;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><table class="form-table">
	<thead>
		<th>
			<?php
			esc_html_e( 'Pricing rules', 'boxtal-connect' );
			?>
			<p class="description"><?php esc_html_e( "Add rules to manage front office price display. The rules will be considered from top to bottom and the first one matching conditions will be applied. If no rules are found, carrier won't be displayed.", 'boxtal-connect' ); ?></p>
		</th>
	</thead>
</table>
<table id="bw-rates-table" class="wc_input_table sortable widefat">
	<thead>
		<tr>
			<th rowspan="2" class="sort">&nbsp;</th>
			<th colspan="2" class="bw-center">
                <?php echo esc_html( 'Cart price Excluding Tax', 'boxtal-connect' ) . ' (' . esc_html( get_woocommerce_currency_symbol() ) . ') '; ?>
			</th>
			<th colspan="2" class="bw-center"><?php echo esc_html( 'Cart weight', 'boxtal-connect' ) . ' (kg)'; ?></th>
			<th rowspan="2" class="bw-center">
			<?php
				echo '<span>' . esc_html( 'Shipping class', 'boxtal-connect' ) . '</span>';
				$tooltip_html  = '<ul><li>' . esc_html( 'if you choose a shipping class, the rule will only apply to carts with all products belonging to the class', 'boxtal-connect' ) . '</li>';
				$tooltip_html .= '<li>' . esc_html( "Beware that newly created shipping classes won't be selected by default", 'boxtal-connect' ) . '</li></ul>';
				Misc_Util::echo_tooltip( $tooltip_html );
			?>
			</th>
			<th class="bw-center"><?php echo esc_html( 'Parcel points', 'boxtal-connect' ); ?></th>
			<th rowspan="2" class="bw-center">
			<?php
				echo '<span>' . esc_html( 'Pricing', 'boxtal-connect' ) . '</span>';
			?>
			</th>
			<th rowspan="2" class="w11 bw-center">
			<?php
				echo '<span class="mr2">' . esc_html( 'Flat rate', 'boxtal-connect' ) . ' (' . esc_html( get_woocommerce_currency_symbol() ) . ')</span>';
				$tooltip_html = __( 'flat rate before taxes', 'boxtal-connect' );
				Misc_Util::echo_tooltip( $tooltip_html );
			?>
			</th>
		</tr>
		<tr>
			<th class="bw-center"><?php esc_html_e( 'From', 'boxtal-connect' ); ?> (≥)</th>
			<th class="bw-center"><?php esc_html_e( 'To', 'boxtal-connect' ); ?> (<)</th>
			<th class="bw-center"><?php esc_html_e( 'From', 'boxtal-connect' ); ?> (≥)</th>
			<th class="bw-center"><?php esc_html_e( 'To', 'boxtal-connect' ); ?> (<)</th>
			<th class="bw-center info-small">
                <?php esc_html_e( 'Associate one or more parcel points networks with your delivery method to display the map', 'boxtal-connect' ); ?>
            </th>
		</tr>
	</thead>
	<tbody class="ui-sortable">
		<?php
		if ( isset( $pricing_items ) && is_array( $pricing_items ) ) {
			$i = 0;
			foreach ( $pricing_items as $pricing_item ) {
				include 'html-admin-shipping-method-rate.php';
				$i++;
			}
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="10">
				<button class="bw-add-rate-line button-secondary"><?php esc_html_e( 'Add rule', 'boxtal-connect' ); ?></button>
				<button class="bw-remove-rate-line button-secondary"><?php esc_html_e( 'Remove selected rule', 'boxtal-connect' ); ?></button>
			</th>
		</tr>
	</tfoot>
</table>
