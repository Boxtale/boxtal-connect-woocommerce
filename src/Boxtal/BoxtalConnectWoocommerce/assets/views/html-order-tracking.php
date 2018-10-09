<?php
/**
 * Order tracking rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-tracking">
    <?php //phpcs:ignore ?>
	<?php if ( property_exists( $tracking, 'shipmentTrackings' ) && ! empty( $tracking->shipmentTrackings ) ) : ?>

        <?php //phpcs:ignore ?>
		<?php if ( 1 === count( $tracking->shipmentTrackings ) ) : ?>
			<p><?php esc_html_e( 'Your order has been sent in 1 shipment.', 'boxtal-connect' ); ?></p>
		<?php else : ?>
            <?php //phpcs:disable ?>
			<?php /* translators: 1) int number of shipments */ ?>
			<p><?php echo esc_html( sprintf( __( 'Your order has been sent in %s shipments.', 'boxtal-connect' ), count( $tracking->shipmentTrackings ) ) ); ?></p>
            <?php //phpcs:enable ?>
		<?php endif; ?>

        <?php //phpcs:ignore ?>
		<?php foreach ( $tracking->shipmentTrackings as $shipment ) : ?>
			<?php //phpcs:ignore ?>
			<h4><?php echo sprintf( __( 'Shipment reference %s', 'boxtal-connect' ), $shipment->carrierReference ); ?></h4>
			<?php $package_count = count( $shipment->packages ); ?>
			<?php if ( 1 === $package_count || 0 === $package_count ) : ?>
				<?php /* translators: 1) int number of shipments */ ?>
				<p><?php echo esc_html( sprintf( __( 'Your shipment has %s package.', 'boxtal-connect' ), $package_count ) ); ?></p>
			<?php else : ?>
				<?php /* translators: 1) int number of shipments */ ?>
				<p><?php echo esc_html( sprintf( __( 'Your shipment has %s packages.', 'boxtal-connect' ), $package_count ) ); ?></p>
			<?php endif; ?>
			<?php foreach ( $shipment->packages as $package ) : ?>
                <?php //phpcs:ignore ?>
				<p><?php echo sprintf( __( 'Package reference %s', 'boxtal-connect' ), '<a href="' . esc_url( $package->trackingUrl ) . '" target="_blank">' . $package->packageReference . '</a>' ); ?></p>
                <?php //phpcs:ignore ?>
				<?php if ( is_array( $package->trackingEvents ) && count( $package->trackingEvents ) > 0 ) : ?>
                    <?php //phpcs:ignore ?>
					<?php foreach ( $package->trackingEvents as $event ) : ?>
						<p>
							<?php
								$date = new DateTime( $event->date );
								echo esc_html( $date->format( __( 'Y-m-d H:i:s', 'boxtal-connect' ) ) . ' ' . $event->message );
							?>
						</p>
					<?php endforeach; ?>

				<?php else : ?>
					<p><?php esc_html_e( 'No tracking event for this package yet.', 'boxtal-connect' ); ?></p>
				<?php endif; ?>
				<br/>
			<?php endforeach; ?>
		<?php endforeach; ?>

	<?php endif; ?>
</div>
