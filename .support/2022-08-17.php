<?php
/**
 * Plugin Name: Default Featured Image - category
 * Plugin URI:  https://wordpress.org/support/topic/fallback-featured-image-by-category/
 * Version:     1.0
 */

add_filter( 'dfi_thumbnail_id', 'dfi_category', 10, 2 );
function dfi_category( $dfi_id, $post_id ) {
	// Set a different image for posts that have the 'cats' category set.
	// This will trigger first, if multiple categories have been set.
	if ( has_category( 'cats', $post_id ) ) {
		return 7; // cats img id.
	}
	// Set a different image for posts that have the 'cats' category set.
	if ( has_category( 'dogs', $post_id ) ) {
		return 8; // dogs img id.
	}

	return $dfi_id; // the original featured image id.
}
