<?php
/**
 * Plugin Name: Default featured image
 * Plugin URI: http://wordpress.org/extend/plugins/default-featured-image/
 * Description: Allows users to select a default featured image in the media settings
 * Version: 1.6.4
 * Requires at least: 4.0
 * Requires PHP: 5.6
 * Author: Jan Willem Oostendorp
 * Author URI: https://janw.me/
 * License: GPLv2 or later
 * Text Domain: default-featured-image
 */
class Default_Featured_Image {
	const VERSION = '1.6.4';

	/**
	 * Hook everything
	 */
	public function __construct() {
		// add the settings field to the media page.
		add_action( 'admin_init', array( &$this, 'media_setting' ) );
		// enqueue the js.
		add_action( 'admin_print_scripts-options-media.php', array( &$this, 'admin_scripts' ) );
		// get the preview image ajax call.
		add_action( 'wp_ajax_dfi_change_preview', array( &$this, 'ajax_wrapper' ) );
		// set dfi meta key on every occasion.
		add_filter( 'get_post_metadata', array( &$this, 'set_dfi_meta_key' ), 10, 4 );
		// display a default featured image.
		add_filter( 'post_thumbnail_html', array( &$this, 'show_dfi' ), 20, 5 );
		// add a link on the plugin page to the setting.
		add_filter( 'plugin_action_links_default-featured-image/set-default-featured-image.php', array( &$this, 'add_settings_link' ), 10, 1 );
		// add L10n.
		add_action( 'init', array( &$this, 'load_plugin_textdomain' ) );
		// remove setting on removal.
		register_uninstall_hook( __FILE__, array( 'default_featured_image', 'uninstall' ) );

	}

	/**
	 * Uninstall
	 */
	public static function uninstall() {
		delete_option( 'dfi_image_id' );
	}

	/**
	 * L10n
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'default-featured-image', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add the dfi_id to the meta data if needed.
	 *
	 * @param null|mixed $null      Should be null, we don't use it because we update the meta cache.
	 * @param int        $object_id ID of the object metadata is for.
	 * @param string     $meta_key  Optional. Metadata key. If not specified, retrieve all metadata for
	 *                              the specified object.
	 * @param bool       $single    Optional, default is false. If true, return only the first value of the
	 *                              specified meta_key. This parameter has no effect if meta_key is not specified.
	 *
	 * @return string|array Single metadata value, or array of values
	 */
	public function set_dfi_meta_key( $null, $object_id, $meta_key, $single ) {
		// Only affect thumbnails on the frontend, do allow ajax calls.
		if ( ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) ) {
			return $null;
		}

		// Check only empty meta_key and '_thumbnail_id'.
		if ( ! empty( $meta_key ) && '_thumbnail_id' !== $meta_key ) {
			return $null;
		}

		// Check if this post type supports featured images.
		if ( ! post_type_supports( get_post_type( $object_id ), 'thumbnail' ) ) {
			return $null; // post type does not support featured images.
		}

		// Get current Cache.
		$meta_cache = wp_cache_get( $object_id, 'post_meta' );

		/**
		 * Empty objects probably need to be initiated.
		 *
		 * @see get_metadata() in /wp-includes/meta.php
		 */
		if ( ! $meta_cache ) {
			$meta_cache = update_meta_cache( 'post', array( $object_id ) );
			if ( isset( $meta_cache[ $object_id ] ) ) {
				$meta_cache = $meta_cache[ $object_id ];
			} else {
				$meta_cache = array();
			}
		}

		// Is the _thumbnail_id present in cache?
		if ( ! empty( $meta_cache['_thumbnail_id'][0] ) ) {
			return $null; // it is present, don't check anymore.
		}

		// Get the Default Featured Image ID.
		$dfi_id = get_option( 'dfi_image_id' );

		// Set the dfi in cache.
		$meta_cache['_thumbnail_id'][0] = apply_filters( 'dfi_thumbnail_id', $dfi_id, $object_id );
		wp_cache_set( $object_id, $meta_cache, 'post_meta' );

		return $null;
	}

	/**
	 * Register the setting on the media settings page.
	 */
	public function media_setting() {
		register_setting(
			'media', // settings page.
			'dfi_image_id', // option name.
			array( &$this, 'input_validation' ) // validation callback.
		);
		add_settings_field(
			'dfi', // id.
			__( 'Default featured image', 'default-featured-image' ), // setting title.
			array( &$this, 'settings_html' ), // display callback.
			'media', // settings page.
			'default' // settings section.
		);
	}

	/**
	 * Display the buttons and a preview on the media settings page.
	 */
	public function settings_html() {
		$value = get_option( 'dfi_image_id' );

		$rm_btn_class = 'button button-disabled';
		if ( ! empty( $value ) ) {
			echo $this->preview_image( $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$rm_btn_class = 'button';
		}
		?>
		<input id="dfi_id" type="hidden" value="<?php echo esc_attr( $value ); ?>" name="dfi_image_id"/>

		<a id="dfi-set-dfi" class="button" title="<?php esc_attr_e( 'Select default featured image', 'default-featured-image' ); ?>" href="#">
			<span style="margin-top: 3px;" class="dashicons dashicons-format-image"></span>
			<?php esc_html_e( 'Select default featured image', 'default-featured-image' ); ?>
		</a>
		<div style="margin-top:5px;">
			<a id="dfi-no-fdi" class="<?php echo esc_attr( $rm_btn_class ); ?>"
				title="<?php esc_attr_e( 'Don\'t use a default featured image', 'default-featured-image' ); ?>" href="#">
				<?php esc_html_e( 'Don\'t use a default featured image', 'default-featured-image' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Is the given input a valid image.
	 *
	 * @param string|int $thumbnail_id The saving thumbnail.
	 *
	 * @return string|bool
	 */
	public function input_validation( $thumbnail_id ) {
		if ( wp_attachment_is_image( $thumbnail_id ) ) {
			return $thumbnail_id;
		}

		return false;
	}

	/**
	 * Register the javascript
	 */
	public function admin_scripts() {
		wp_enqueue_media(); // scripts used for uploader.
		wp_enqueue_script( 'dfi-script', plugin_dir_url( __FILE__ ) . 'set-default-featured-image.js', array(), self::VERSION, true );
		wp_localize_script(
			'dfi-script',
			'dfi_L10n',
			array(
				'manager_title'  => __( 'Select default featured image', 'default-featured-image' ),
				'manager_button' => __( 'Set default featured image', 'default-featured-image' ),
			)
		);
	}

	/**
	 * Get an image and wrap it in a div
	 *
	 * @param int $image_id A valid attachment image ID.
	 *
	 * @return string
	 */
	public function preview_image( $image_id ) {
		$output  = '<div id="preview-image" style="float:left; padding: 0 5px 0 0;">';
		$output .= wp_get_attachment_image( $image_id, array( 80, 60 ), true );
		$output .= '</div>';

		return $output;
	}

	/**
	 * The callback for the ajax call when the DFI changes
	 */
	public function ajax_wrapper() {
		if ( ! empty( $_POST['image_id'] ) && absint( $_POST['image_id'] ) ) {
			$img_id = absint( $_POST['image_id'] );
			echo $this->preview_image( $img_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		die(); // ajax call..
	}

	/**
	 * Add a settings link to the the plugin on the plugin page
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array
	 */
	public function add_settings_link( $links ) {
		$href          = admin_url( 'options-media.php#dfi-set-dfi' );
		$settings_link = '<a href="' . $href . '">' . __( 'Settings' ) . '</a>'; // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Set a default featured image if it is missing
	 *
	 * @param string $html              The post thumbnail HTML.
	 * @param int    $post_id           The post ID.
	 * @param int    $post_thumbnail_id The post thumbnail ID.
	 * @param string $size              The post thumbnail size. Image size or array of width and height.
	 * @param array  $attr              values (in that order). Default 'post-thumbnail'.
	 *
	 * @return string
	 */
	public function show_dfi( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		$default_thumbnail_id = get_option( 'dfi_image_id' ); // select the default thumb.

		// if an image is set return that image.
		if ( (int) $default_thumbnail_id !== (int) $post_thumbnail_id ) {
			return $html;
		}

		if ( isset( $attr['class'] ) ) {
			$attr['class'] .= ' default-featured-img';
		} else {
			$size_class = $size;
			if ( is_array( $size_class ) ) {
				$size_class = 'size-' . implode( 'x', $size_class );
			}
			// attachment-$size is a default class `wp_get_attachment_image` would otherwise add. It won't add it if there are classes already there.
			$attr = array( 'class' => "attachment-{$size_class} default-featured-img" );
		}

		$html = wp_get_attachment_image( $default_thumbnail_id, $size, false, $attr );
		$html = apply_filters( 'dfi_thumbnail_html', $html, $post_id, $default_thumbnail_id, $size, $attr );

		return $html;
	}
}

// run the plugin class.
new Default_Featured_Image();
