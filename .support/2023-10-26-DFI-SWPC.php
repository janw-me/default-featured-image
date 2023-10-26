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
  * As a default set the DFI, this will be overwritten if a normal Thumbnail is set.
  *
  * @param array $data Post data before it's filled.
  * @param array $blog Current blog details.
  *
  * @see Rudr_Simple_WP_Crosspost::add_featured_image
  */
function dfi_category( $data, $blog ) {
	$dfi_id = get_option( 'dfi_image_id' );
	if ( empty( $dfi_id ) ) {
		return $data; // No DFI set
	}

	$new_featured_image = Rudr_Simple_WP_Crosspost::maybe_crosspost_image( $dfi_id, $blog );

	$data[ 'featured_image' ] = $new_featured_image[ 'id' ];
}
add_filter( 'rudr_swc_pre_crosspost_post_data', 'dfi_category', 10, 2 );
