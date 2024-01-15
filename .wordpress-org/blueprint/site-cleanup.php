<?php
/**
 * This file will cleanup settings that the importer cannot fix.
 */

wp_delete_post( 1, true ); // Delete Hello world
wp_delete_post( 2, true ); // Delete Sample Page.
wp_delete_post( 3, true ); // Delete Privacy Policy

update_option( 'blogname', 'Default Featured Image Demo Site' );

// Get the post of the DFI
$dfi_posts = get_posts( array( 'post_title' => 'dfi', 'post_type' => 'attachment', 'posts_per_page' => 1 ) );
if ( ! empty( $dfi_posts ) ) {
    update_option( 'dfi_image_id', $dfi_posts[0]->ID );
}
