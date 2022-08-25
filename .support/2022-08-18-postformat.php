<?php
/**
 * Plugin Name: Default Featured Image - Video format
 * Plugin URI:  https://wordpress.org/support/topic/only-use-featured-image-for-video-post-format-please/
 * Version:     1.0
 */

add_filter( 'dfi_thumbnail_id', 'dfi_postformats', 10, 2 );
function dfi_postformats( $dfi_id, $post_id ) {

	if ( has_post_format( 'video', $post_id ) ) {
		return $dfi_id; // DFI set in the media settings for videos.
	}

	return 0; // ignore all others.
}
