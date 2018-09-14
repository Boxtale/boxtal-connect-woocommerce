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
	<?php if ( null !== $tracking ) : ?>

		<?php if ( 1 === count( $tracking ) ) : ?>
			<p><?php esc_html_e( 'Your order has been sent in 1 shipment.', 'boxtal-woocommerce' ); ?></p>
		<?php else : ?>
			<?php /* translators: 1) int number of shipments */ ?>
			<p><?php echo esc_html( sprintf( __( 'Your order has been sent in %s shipments.', 'boxtal-woocommerce' ), count( $tracking ) ) ); ?></p>
		<?php endif; ?>

		<?php foreach ( $tracking as $shipment ) : ?>
			<?php //phpcs:ignore ?>
			<h4><?php echo sprintf( __( 'Shipment reference %s', 'boxtal-woocommerce' ), '<a href="' . esc_url( "anyurl" ) . '" target="_blank">' . $shipment->carrierReference . '</a>' ); ?></h4>
			<?php if ( 1 === count( $shipment->packages ) ) : ?>
				<p><?php esc_html_e( 'Your shipment has 1 package.', 'boxtal-woocommerce' ); ?></p>
			<?php else : ?>
				<?php /* translators: 1) int number of shipments */ ?>
				<p><?php echo esc_html( sprintf( __( 'Your shipment has %s packages.', 'boxtal-woocommerce' ), count( $shipment->packages ) ) ); ?></p>
			<?php endif; ?>
			<?php foreach ( $shipment->packages as $package ) : ?>
                <?php //phpcs:ignore ?>
				<p><?php echo sprintf( __( 'Package reference %s:', 'boxtal-woocommerce' ), $package->packageReference ); ?></p>
                <?php //phpcs:ignore ?>
				<?php if ( is_array( $package->trackingEvents ) && count( $package->trackingEvents ) > 0 ) : ?>
                    <?php //phpcs:ignore ?>
					<?php foreach ( $package->trackingEvents as $event ) : ?>
						<p>
							<?php
								$date = new DateTime( $event->date );
								echo esc_html( $date->format( __( 'Y-m-d H:i:s', 'boxtal-woocommerce' ) ) . ' ' . $event->message );
							?>
						</p>
					<?php endforeach; ?>

				<?php else : ?>
					<p><?php esc_html_e( 'No tracking event for this shipment yet.', 'boxtal-woocommerce' ); ?></p>
				<?php endif; ?>
				<br/>
			<?php endforeach; ?>
		<?php endforeach; ?>

	<?php endif; ?>
</div>
