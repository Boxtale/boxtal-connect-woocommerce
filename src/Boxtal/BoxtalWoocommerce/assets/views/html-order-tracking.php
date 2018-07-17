<?php
/**
 * Order tracking rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-tracking">
	<?php if ( null === $tracking ) : ?>

		<p><?php esc_html_e( 'No tracking info available yet', 'boxtal-woocommerce' ); ?></p>

	<?php else : ?>

		<?php if ( 1 === count( $tracking ) ) : ?>
			<p><?php esc_html_e( 'Your order has been sent in 1 shipment.', 'boxtal-woocommerce' ); ?></p>
		<?php else : ?>
			<?php /* translators: 1) int number of shipments */ ?>
			<p><?php echo esc_html( sprintf( __( 'Your order has been sent in %s shipments.', 'boxtal-woocommerce' ), count( $tracking ) ) ); ?></p>
		<?php endif; ?>

		<?php foreach ( $tracking as $shipment ) : ?>
			<?php //phpcs:ignore ?>
			<h4><?php echo sprintf( __( 'Shipment reference %s', 'boxtal-woocommerce' ), '<a href="' . esc_url( $shipment['tracking_url'] ) . '" target="_blank">' . $shipment['reference'] . '</a>' ); ?></h4>
			<?php if ( isset( $shipment['tracking_events'] ) && is_array( $shipment['tracking_events'] ) && count( $shipment['tracking_events'] ) > 0 ) : ?>

				<?php foreach ( $shipment['tracking_events'] as $event ) : ?>
					<p>
						<?php
							echo esc_html( $event->date . ' ' . $event->message );
						?>
					</p>
				<?php endforeach; ?>

			<?php else : ?>
				<p><?php esc_html_e( 'No tracking event for this shipment yet.', 'boxtal-woocommerce' ); ?></p>
			<?php endif; ?>
		<?php endforeach; ?>

	<?php endif; ?>
</div>
