<?php
/**
 * Plugin Name: Default Featured Image - Random per category
 * Plugin URI:  https://wordpress.org/support/topic/multiple-images-for-different-categories/
 * Version:     1.0
 */

add_filter( 'dfi_thumbnail_id', 'dfi_category_random', 10, 2 );
function dfi_category_random( $dfi_id, $post_id ) {
    // Set a different image for posts that have the 'cats' category set.
    if ( has_category( 'cats', $post_id ) ) {
        $random_cats = array(
            7,
            8,
        );
        return $random_cats[ array_rand( $random_cats ) ];
    }
    // Set a different image for posts that have the 'dogs' category set.
    if ( has_category( 'dogs', $post_id ) ) {
        $random_dogs = array(
            10,
            14,
        );
        return $random_dogs[ array_rand( $random_dogs ) ];
    }

    return $dfi_id; // the original featured image id.
}
