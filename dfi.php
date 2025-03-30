<?php
/**
 * The actual plugin file.
 *
 * @package DFI
 */
function dfi_load() {

	defined( 'DFI_VERSION' ) || define( 'DFI_VERSION', '1.8.1' );
	defined( 'DFI_DIR' ) || define( 'DFI_DIR', plugin_dir_path( __FILE__ ) );
	defined( 'DFI_URL' ) || define( 'DFI_URL', plugin_dir_url( __FILE__ ) );

	require_once DFI_DIR . 'app' . DIRECTORY_SEPARATOR . 'class-dfi.php';
	require_once DFI_DIR . 'app' . DIRECTORY_SEPARATOR . 'class-dfi-exceptions.php';

	$dfi = DFI::instance();

	// add the settings field to the media page.
	add_action( 'admin_init', array( $dfi, 'media_setting' ) );
	add_action( 'rest_api_init', array( $dfi, 'register_media_setting' ) );
	// enqueue the js.
	add_action( 'admin_print_scripts-options-media.php', array( $dfi, 'admin_scripts' ) );
	// get the preview image ajax call.
	add_action( 'wp_ajax_dfi_change_preview', array( $dfi, 'ajax_wrapper' ) );
	// set dfi meta key on every occasion.
	add_filter( 'get_post_metadata', array( $dfi, 'set_dfi_meta_key' ), 10, 3 );
	// display a default featured image.
	add_filter( 'post_thumbnail_html', array( $dfi, 'show_dfi' ), 20, 5 );
	// add a link on the plugin page to the setting.
	add_filter( 'plugin_action_links_default-featured-image/set-default-featured-image.php', array( $dfi, 'add_settings_link' ) );
	// add L10n.
	add_action( 'init', array( $dfi, 'load_plugin_textdomain' ) );

	/**
	 * Exception: https://wordpress.org/plugins/wp-user-frontend/
	 *
	 * @see https://wordpress.org/support/topic/couldnt-able-to-edit-default-featured-image-from-post/
	 */
	add_filter( 'pre_do_shortcode_tag', array( 'DFI_Exceptions', 'wp_user_frontend_pre' ), 9, 2 );
	add_filter( 'do_shortcode_tag', array( 'DFI_Exceptions', 'wp_user_frontend_after' ), 9, 2 );

	/**
	 * Exception: https://www.wpallimport.com/
	 *
	 * @see https://wordpress.org/support/topic/importing-images-into-woocommerce-using-cron/
	 */
	add_filter( 'dfi_thumbnail_id', array( 'DFI_Exceptions', 'wp_all_import_dfi_workaround' ), 9 );
}

add_action( 'plugins_loaded', 'dfi_load', 11 );
