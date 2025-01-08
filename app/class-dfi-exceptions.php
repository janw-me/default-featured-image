<?php

/**
 * This class holds exceptions for plugins that need exceptions for DFI.
 *
 * Class DFI_Exceptions
 */
class DFI_Exceptions {

	/**
	 * Exclude dfi from shortcode: wpuf_edit
	 *
	 * @param mixed  $false unused, just pass along.
	 * @param string $tag The shortcode.
	 *
	 * @return mixed
	 */
	public static function wp_user_frontend_pre( $false, $tag ) {
		if ( 'wpuf_edit' === $tag ) {
			add_filter( 'dfi_thumbnail_id', '__return_null' );
		}

		return $false;
	}

	/**
	 * Exclude dfi from shortcode: wpuf_edit
	 *
	 * @param mixed  $output unused, just pass along.
	 * @param string $tag The shortcode.
	 *
	 * @return mixed
	 */
	public static function wp_user_frontend_after( $output, $tag ) {
		if ( 'wpuf_edit' === $tag ) {
			remove_filter( 'dfi_thumbnail_id', '__return_null' );
		}

		return $output;
	}

	/**
	 * Exclude wp all import, the DFI during the import.
	 *
	 * @param int $dfi_id The DFI id.
	 *
	 * @return null|int
	 */
	public static function wp_all_import_dfi_workaround( $dfi_id ) {
		if ( function_exists( 'wp_all_import_get_import_id' ) && is_numeric( wp_all_import_get_import_id() ) ) {
			return null; // If a post is imported with WP All Import, set DFI id to null.
		} else {
			return $dfi_id;
		}
	}
}
