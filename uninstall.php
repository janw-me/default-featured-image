<?php
/**
 * Uninstall the plugin.
 *
 * @package DFI
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly.
}

// delete the settings.
delete_option( 'dfi_image_id' );
