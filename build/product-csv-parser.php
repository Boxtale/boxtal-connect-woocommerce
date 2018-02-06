<?php
/**
 * PHP script which aims at importing woocommerce sample products data
 *
 * @package Boxtal\BoxtalWoocommerce
 */

if ( isset( $argv[1] ) ) {
	$handle = fopen( dirname( __DIR__ ) . '/' . $argv[1], 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
	if ( false !== $handle ) {
		$row  = 0;
		$data = fgetcsv( $handle, 1000, ',', '"' );
		while ( false !== $data ) {
			if ( 0 === $row ) {
				continue;
			}
			list($type, $sku, $name, $published, $is_featured, $visibility, $short_desc, $long_desc, , , , , , , , , $weight, , , , , , , $regular_price, , $tags, , $images) = $data;
			shell_exec( dirname( __DIR__ ) . '/vendor/wp-cli/wp-cli/bin/wp wc product create --name="' . $name . '" --type="' . $type . '" --status="' . $published . '" --featured="' . $is_featured . '" --catalog_visibility="' . $visibility . '" --description="' . $long_desc . '" --short_description="' . $short_desc . '" --sku="' . $sku . '" --regular_price="' . $regular_price . '" --weight="' . $weight . '" --tags="' . $tags . '" --images="' . $images . '"' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_shell_exec
			$row++;
		}
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
	}
}

