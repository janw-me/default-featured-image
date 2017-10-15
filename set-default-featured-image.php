<?php /** @noinspection PhpUndefinedClassInspection */

/**
 * plugin name: Default featured image
 * Plugin URI: http://wordpress.org/extend/plugins/default-featured-image/
 * Description: Allows users to select a default featured image in the media settings
 * Version: 1.6.1
 * Requires PHP: 5.5
 * Author: Jan Willem Oostendorp
 * License: GPLv2 or later
 * Text Domain: default-featured-image
 */

class default_featured_image
{
	const L10n = 'default-featured-image';

	/**
	 * Hook everything
	 */
	function __construct() {
		// add the settings field to the media page
		add_action( 'admin_init', array( &$this, 'media_setting' ) );
		// enqueue the js
		add_action( 'admin_print_scripts-options-media.php', array( &$this, 'admin_scripts' ) );
		// get the preview image ajax call
		add_action( 'wp_ajax_dfi_change_preview', array( &$this, 'ajax_wrapper' ) );
		// set dfi meta key on every occasion
		add_filter( 'get_post_metadata', array(&$this, 'set_dfi_meta_key'), 10, 4 );
		// display a default featured image
		add_filter( 'post_thumbnail_html', array( &$this, 'show_dfi' ), 20, 5 );
		// add a link on the plugin page to the setting
		add_filter('plugin_action_links', array(&$this, 'add_settings_link'), 10, 2 );
		// add L10n
		add_action( 'init', array(&$this, 'L10n') );
		// remove setting on removal
		register_uninstall_hook(__FILE__, array('default_featured_image', 'uninstall'));

	}

	static function uninstall() {
		delete_option( 'dfi_image_id' );
	}

	function L10n() {
		load_plugin_textdomain(self::L10n, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Mostly the same as `get_metadata()` makes sure any post thumbnail function gets checked at
	 * the deepest level possible.
	 *
	 * @see /wp-includes/meta.php get_metadata()
	 *
	 * @param null $null
	 * @param int $object_id ID of the object metadata is for
	 * @param string $meta_key Optional. Metadata key. If not specified, retrieve all metadata for
	 *   the specified object.
	 * @param bool $single Optional, default is false. If true, return only the first value of the
	 *   specified meta_key. This parameter has no effect if meta_key is not specified.
	 * @return string|array Single metadata value, or array of values
	 */
	function set_dfi_meta_key( $null = null, $object_id, $meta_key, $single ) {
		// only affect thumbnails on the frontend, do allow ajax calls
		if ( ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) || '_thumbnail_id' != $meta_key )
			return $null;

		$meta_type = 'post';
		$meta_cache = wp_cache_get($object_id, $meta_type . '_meta');

		if ( !$meta_cache ) {
			$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
			$meta_cache = $meta_cache[$object_id];
		}

		if ( !$meta_key )
			return $meta_cache;

		if ( isset($meta_cache[$meta_key]) ) {
			if ( $single )
				return maybe_unserialize( $meta_cache[$meta_key][0] );
			else
				return array_map('maybe_unserialize', $meta_cache[$meta_key]);
		}

		if ($single)
			// allow to set an other ID see the readme.txt for details
			return apply_filters( 'dfi_thumbnail_id', get_option( 'dfi_image_id' ), $object_id ); // set the default featured img ID
		else
			return array();
	}

	/**
	 * register the setting on the media settings page
	 */
	function media_setting() {
		register_setting(
		'media', // settings page
		'dfi_image_id', // option name
		array( &$this, 'input_validation' ) // validation callback
		);
		add_settings_field(
		'dfi', // id
		__( 'Default featured image', self::L10n ), // setting title
		array( &$this, 'settings_html' ), // display callback
		'media', // settings page
		'default' // settings section
		);
	}

	/**
	 * display the buttons and a preview
	 */
	function settings_html() {
		$value = get_option( 'dfi_image_id' );

		$rm_btn_class = 'button button-disabled';
		if ( !empty( $value ) ) {
			echo $this->preview_image( $value );
			$rm_btn_class = 'button';
		}
		?>
		<input id="dfi_id" type="hidden" value="<?php echo esc_attr( $value ); ?>" name="dfi_image_id"/>

		<a id="dfi-set-dfi" class="button" title="<?php _e( 'Select default featured image', self::L10n ) ?>" href="#">
			<span style="margin-top: 3px;" class="dashicons dashicons-format-image"></span>
			<?php _e( 'Select default featured image', self::L10n ) ?>
		</a>
		<div style="margin-top:5px;">
			<a id="dfi-no-fdi" class="<?php echo $rm_btn_class ?>" title="<?php _e( 'Don\'t use a default featured image', self::L10n ) ?>" href="#">
				<?php _e( 'Don\'t use a default featured image', self::L10n ) ?>
			</a>
		</div>
		<?php
	}

	// Validate user input
	function input_validation( $input ) {
		if ( wp_attachment_is_image( $input) ) {
			return $input;
		}
		return false;
	}

	/**
	 * Register the javascript
	 */
	function admin_scripts() {
		wp_enqueue_media(); // scripts used for uploader
		wp_enqueue_script( 'dfi-script', plugin_dir_url( __FILE__ ) . 'set-default-featured-image.js' );
		wp_localize_script('dfi-script', 'dfi_L10n', array(
			'manager_title' => __('Select default featured image', self::L10n),
			'manager_button' => __('Set default featured image', self::L10n),
		));
	}

	/**
	 * get an image and wrap it in a div
	 * @param int $image_id
	 * @return string
	 */
	function preview_image( $image_id ) {
		$output = '<div id="preview-image" style="float:left; padding: 0 5px 0 0;">';
		$output .= wp_get_attachment_image( $image_id, array( 80, 60 ), true );
		$output .= '</div>';
		return $output;
	}

	/**
	 * The callback for the ajax call when the DFI changes
	 */
	function ajax_wrapper() {
		if ( isset( $_POST['image_id'] ) ) {
			echo $this->preview_image( $_POST['image_id'] );
		}
		die(); // ajax call
	}

	/**
	 * add a settings link to the the plugin on the plugin page
	 * @param array $links
	 * @param string $file
	 * @return array
	 */
	function add_settings_link( $links, $file ) {

		if ( $file == plugin_basename( __FILE__ ) ) {
		    $href = admin_url('options-media.php#dfi-set-dfi');
			$settings_link = '<a href="' . $href . '">' . __( 'Settings' )/*get this from WP core*/ . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Set a default featured image if it is missing
	 * @param string $html
	 * @param int $post_id
	 * @param int $post_thumbnail_id
	 * @param string $size
	 * @param array $attr
	 * @return string
	 */
	function show_dfi( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		$default_thumbnail_id = get_option( 'dfi_image_id' ); //select the default thumb

		// if an image is set return that image
		if ( $default_thumbnail_id != $post_thumbnail_id )
			return $html;

		if (isset($attr['class']) ) {
			$attr['class'] .= " default-featured-img";
		} else {
			$size_class = $size;
			if ( is_array( $size_class )) {
				$size_class = 'size-' . implode( 'x', $size_class);
			}
			//attachment-$size is a default class `wp_get_attachment_image` would otherwise add. It won't add it if there are classes already there
			$attr = array ('class' => "attachment-{$size_class} default-featured-img");
		}

		$html = wp_get_attachment_image( $default_thumbnail_id, $size, false, $attr );
		$html = apply_filters( 'dfi_thumbnail_html', $html, $post_id, $default_thumbnail_id, $size, $attr );

		return $html;
	}
}
//run the plugin class
new default_featured_image();
