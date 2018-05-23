<?php
/**
 * Pairing notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ($notice->result): ?>
    <div class="bw-notice bw-success">
        <?php esc_html_e( 'Congratulations! You\'ve successfully paired your site with Boxtal.', 'boxtal-woocommerce' ); ?>
        <p>
            <a class="button-secondary bw-hide-notice" rel="pairing">
                <?php esc_html_e( 'Hide this notice', 'boxtal-woocommerce' ); ?>
            </a>
        </p>
    </div>
<?php else: ?>
    <div class="bw-notice bw-warning">
        <?php esc_html_e( 'Pairing with Boxtal is not complete. Please check your Boxtal Woocommerce connector in your boxtal account for a more complete diagnostic.', 'boxtal-woocommerce' ); ?>
    </div>
<?php endif ?>