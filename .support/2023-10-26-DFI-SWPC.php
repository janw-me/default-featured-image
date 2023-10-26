<?php
/**
 * Plugin Name: Default Featured Image - Simple WordPress Crossposting Bridge
 * Description: Make sure the DFI is in a import of Simple WordPress Crossposting.
 * Author:      Jan Willem Oostendorp
 * Author URI:  https://janw.me/
 * License:     GPLv2 or later
 * Version:     1.0
 */

 /**
  * As a default set the DFI, this will be overwritten if a DFI is set.
  *
  * @param array $data Post data before it's filled.
  */
function dfi_category( $data ) {
	$dfi_id = get_option( 'dfi_image_id' );
	if ( ! empty( $dfi_id ) ) {
		$data[ 'featured_image' ] = dfi_id;
	}

	return $data;
}
add_filter( 'rudr_swc_pre_crosspost_post_data', 'dfi_category', 10, 1 );
