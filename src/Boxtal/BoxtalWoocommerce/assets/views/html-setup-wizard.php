<?php
/**
 * Setup wizard rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta name="viewport" content="width=device-width" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php esc_html_e( 'Boxtal Woocommerce &rsaquo; Setup Wizard', 'boxtal-woocommerce' ); ?></title>
		<?php do_action( 'admin_print_styles' ); ?>
		<?php do_action( 'admin_head' ); ?>
	</head>
	<body class="bw-setup">
		<div class="bw-setup-content">
			<h1 id="bw-logo"><?php esc_html_e( 'Your all-in-one shipping provider', 'boxtal-woocommerce' ); ?></h1>
			<?php
				/* translators: 1) string "%" */
				$message1 = sprintf( __( '<strong>Up to 75%s</strong> immediate discount on your shipments', 'boxtal-woocommerce' ), '%' );
                // phpcs:ignore
                echo '<p>'.$message1.'</p>';
			?>
			<p><?php esc_html_e( 'No minimum volume, no contract', 'boxtal-woocommerce' ); ?></p>

			<?php
			$message2 = __( '<strong>Easy and transparent</strong> package monitoring', 'boxtal-woocommerce' );
            // phpcs:ignore
            echo '<p>'.$message2.'</p>';
			$message3 = __( "<strong>1 dedicated customer service team</strong> answering your customers' needs and <strong>one single billing</strong> whichever carriers you choose, <strong>let Boxtal handle everything!</strong>", 'boxtal-woocommerce' );
            // phpcs:ignore
            echo '<p>'.$message3.'</p>';
			?>
			<hr>
			<div class="bw-setup-actions">
				<table>
					<tr>
						<td><a href="<?php echo esc_url( $this->account_creation_link ); ?>" class="button-primary button button-large button-next"><?php esc_html_e( 'Create a free account', 'boxtal-woocommerce' ); ?></a></td>
						<td><a href="<?php echo esc_url( $this->connect_shop_link ); ?>" class="button-primary button button-large"><?php esc_html_e( 'I already have an account', 'boxtal-woocommerce' ); ?></a></td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
