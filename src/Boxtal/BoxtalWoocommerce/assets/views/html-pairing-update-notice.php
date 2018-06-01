<?php
/**
 * Pairing update notice rendering
 *
 * @package     Boxtal\BoxtalWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-notice bw-warning">
    <?php echo sprintf(__('You need to update your pairing in order to be able to sync with Boxtal. Click %s.', 'boxtal-woocommerce' ), '<a class="bw-modal-trigger" bw-modal-target="bw-pairing-update-modal" href="#">' . __('here', 'boxtal-woocommerce') . '</a>' ); ?>
</div>

<div id="bw-pairing-update-modal" class="bw-modal">
    <div class="bw-modal-content">
        <h2 class="content-horizontal-center"><?php esc_html_e( 'Enter validation code', 'boxtal-woocommerce' ); ?></h2>
        <div class="content-horizontal-center">
            <?php
                $i = 0;
                while ( $i < 6 ) {
                    // phpcs:ignore
                    echo '<input type="number" name="bw-digit-' . $i . '" min="0" max="9" size="1" maxlength="1" step="1" oninput="this.value=this.value.substring(0,1);" />';
                    $i++;
                }
            ?>
        </div>
        <div class="content-horizontal-center bw-modal-actions">
            <div class="button-primary" id="bw-pairing-update-validate"><?php esc_html_e( 'OK', 'boxtal-woocommerce' ); ?></div>
        </div>
    </div>
</div>