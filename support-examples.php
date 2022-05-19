<?php
/**
 * This file functions like a bunch of snippets for forum questions.
 *
 * phpcs:disable Generic.Commenting.DocComment.MissingShort
 * phpcs:disable Squiz.Commenting
 * phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
 * phpcs:disable WordPress.PHP.YodaConditions.NotYoda
 */

/*
= Can I set a different image for a custom post type?
Yes, the following code will set a different image.
*/
add_filter( 'dfi_thumbnail_id', 'dfi_posttype_book', 10, 2 );
function dfi_posttype_book( $dfi_id, $post_id ) {
	$post = get_post( $post_id );
	if ( 'book' === $post->post_type ) {
		return 31; // the image id for the book post type.
	}

	return $dfi_id; // the original featured image id.
}

/*
= Can I set different images per category? =
Yes, the following snippet will set different images based on the category.
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

/*
= Can I exclude one page from having a Default Featured Image? =
The following code will exclude the post/page with ID 23.
*/
add_filter( 'dfi_thumbnail_id', 'dfi_skip_page', 10, 2 );
function dfi_skip_page( $dfi_id, $post_id ) {
	if ( $post_id == 23 ) {
		return 0; // invalid id.
	}

	return $dfi_id; // the original featured image id.
}

/*
= Can I change the HTML of the default featured image? =
When a Default Featured Image is used it will already add an extra class `default-featured-img`.
This can be used for styling.

If you need more you can change the whole HTML with the filter `dfi_thumbnail_html`.
*/
add_filter( 'dfi_thumbnail_html', 'dfi_add_class', 10, 5 );
function dfi_add_class( $html, $post_id, $default_thumbnail_id, $size, $attr ) {
	// Add a class to the existing class list.
	$attr['class'] .= ' my-class';

	return wp_get_attachment_image( $default_thumbnail_id, $size, false, $attr );
}

