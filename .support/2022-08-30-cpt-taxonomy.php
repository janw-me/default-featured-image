<?php
/**
 * Plugin Name: Default Featured Image - custom taxonomy
 * Plugin URI:  https://wordpress.org/support/topic/default-images-for-custom-post-categories/
 * Version:     1.0
 */

add_filter( 'dfi_thumbnail_id', 'dfi_listing_category', 10, 2 );
function dfi_listing_category( $dfi_id, $post_id ) {
	// Set a different image for posts that have the 'cats' category set.
	// This will trigger first, if multiple categories have been set.
	if ( has_term( 'cats', 'listing-category', $post_id ) ) {
		return 7; // cats img id.
	}
	// Set a different image for posts that have the 'cats' category set.
	if ( has_term( 'dogs', 'listing-category', $post_id ) ) {
		return 8; // dogs img id.
	}

	return $dfi_id; // the original featured image id.
}
