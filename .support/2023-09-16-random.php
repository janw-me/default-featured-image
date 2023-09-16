<?php
/**
 * Plugin Name: Default Featured Image - Random post image
 * Plugin URI:  https://wordpress.org/support/topic/multiple-featured-images-6/
 * Version:     1.0
 */

add_filter( 'dfi_thumbnail_id', 'dfi_random_image', 10, 2 );
function dfi_random_image( $dfi_id, $post_id ) {
	if ( get_post_type( $post_id ) !== 'post' ) {
		return $dfi_id; // This is not a post, we only check posts.
	}
	$random_image_ids = array(
		123, # Add your image id's in this array.
		456,
	);

	return $random_image_ids[ array_rand( $random_image_ids ) ];
}
