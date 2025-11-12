<?php
/**
 * Plugin Name: Default Featured Image - Exclude rest API
 * Plugin URI:  https://wordpress.org/support/topic/remove-dfi-from-rest-api/
 * Version:     1.0
 */

add_filter( 'dfi_thumbnail_id', 'dfi_disable_in_rest', 10, 2 );
function dfi_disable_in_rest( $dfi_id, $post_id ) {
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return 0; // Empty image ID for rest requests.
	}

    return $dfi_id; // the original featured image id.
}

