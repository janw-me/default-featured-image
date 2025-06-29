<?php

/**
 * Class DFI
 */
final class DFI {

	/**
	 * The option name.
	 */
	const OPTION = 'dfi_image_id';

	/**
	 * Holds the instance.
	 *
	 * @var self
	 */
	protected static $inst;

	/**
	 * Create instance of this class.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$inst ) {
			self::$inst = new self();
		}
		return self::$inst;
	}

	/**
	 * The constructor
	 */
	private function __construct() {
	}

	/**
	 * L10n
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'default-featured-image', false, plugin_basename( DFI_DIR ) . '/languages/' );
	}

	/**
	 * Get the DFI ID.
	 */
	public function get_dfi_id(): int {
		$dfi_id = get_option( self::OPTION );
		if ( ! ctype_digit( $dfi_id ) || empty( $dfi_id ) ) {
			return 0; // No valid ID, return 0.
		}
		return absint( $dfi_id );
	}

	/**
	 * Add the dfi_id to the meta data if needed.
	 *
	 * @param null|mixed $_null      Should be null, we don't use it because we update the meta cache.
	 * @param int        $object_id ID of the object metadata is for.
	 * @param string     $meta_key  Optional. Metadata key. If not specified, retrieve all metadata for
	 *                              the specified object.
	 *
	 * @return null|mixed Single metadata value, or array of values
	 */
	public function set_dfi_meta_key( $_null, $object_id, $meta_key ) {
		// Check only empty meta_key and '_thumbnail_id'.
		if ( ! empty( $meta_key ) && '_thumbnail_id' !== $meta_key ) {
			return $_null;
		}

		// Only affect thumbnails on the frontend, do allow ajax calls.
		if ( ( is_admin() && ! wp_doing_ajax() ) ) {
			return $_null;
		}

		$post_type = get_post_type( $object_id );
		// Check if this post type supports featured images.
		if ( false !== $post_type && ! post_type_supports( $post_type, 'thumbnail' ) ) {
			return $_null; // post type does not support featured images.
		}

		$meta_cache = wp_cache_get( $object_id, 'post_meta' );

		/**
		 * Empty objects probably need to be initiated.
		 *
		 * @see get_metadata() in /wp-includes/meta.php
		 */
		if ( ! $meta_cache ) {
			$meta_cache = update_meta_cache( 'post', array( $object_id ) );
			if ( ! empty( $meta_cache[ $object_id ] ) ) {
				$meta_cache = $meta_cache[ $object_id ];
			} else {
				$meta_cache = array();
			}
		}

		// Is the _thumbnail_id present in cache?
		if ( ! empty( $meta_cache['_thumbnail_id'][0] ) ) {
			return $_null; // it is present, don't check anymore.
		}

		// Set the dfi in cache.
		$meta_cache['_thumbnail_id'][0] = apply_filters( 'dfi_thumbnail_id', $this->get_dfi_id(), $object_id );
		wp_cache_set( $object_id, $meta_cache, 'post_meta' );

		return $_null;
	}

	/**
	 * Register the DFI option.
	 *
	 * @return void
	 */
	public function register_media_setting() {
		register_setting(
			'media', // settings page.
			self::OPTION, // option name.
			array(
				'sanitize_callback' => array( &$this, 'input_validation' ),
				'show_in_rest'      => array(
					'schema' => array(
						'type'    => 'integer',
						'minimum' => 1,
					),
				),
			)
		);
	}

	/**
	 * Register the setting on the media settings page.
	 *
	 * @return void
	 */
	public function media_setting() {
		$this->register_media_setting();
		add_settings_field(
			'dfi', // id.
			_x( 'Default featured image', 'Label on the settings page.', 'default-featured-image' ), // setting title.
			array( &$this, 'settings_html' ), // display callback.
			'media', // settings page.
			'default', // settings section.
		);
	}

	/**
	 * Display the buttons and a preview on the media settings page.
	 *
	 * @return void
	 */
	public function settings_html() {
		$dfi_id = $this->get_dfi_id();

		$rm_btn_class = 'button button-disabled';
		if ( ! empty( $dfi_id ) ) {
			echo $this->preview_image( $dfi_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$rm_btn_class = 'button';
		}
		?>
		<input id="dfi_id" type="hidden" value="<?php echo absint( $dfi_id ); ?>" name="<?php echo esc_attr( $this::OPTION ); ?>"/>

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
	 * @return int|false
	 */
	public function input_validation( $thumbnail_id ) {
		if ( wp_attachment_is_image( absint( $thumbnail_id ) ) ) {
			return absint( $thumbnail_id );
		}

		return false;
	}

	/**
	 * Register the javascript
	 *
	 * @return void
	 */
	public function admin_scripts() {
		wp_enqueue_media(); // scripts used for uploader.
		wp_enqueue_script( 'dfi-script', DFI_URL . 'src/dfi-admin.js', array(), DFI_VERSION, true );
		wp_localize_script(
			'dfi-script',
			'dfi_L10n',
			array(
				'manager_title'  => __( 'Select default featured image', 'default-featured-image' ),
				'manager_button' => __( 'Set default featured image', 'default-featured-image' ),
			),
		);
	}

	/**
	 * Get an image and wrap it in a div
	 *
	 * @param int $image_id A valid attachment image ID.
	 *
	 * @return string
	 */
	public function preview_image( int $image_id ) {
		$output  = '<div id="preview-image" style="float:left; padding: 0 5px 0 0;">';
		$output .= wp_get_attachment_image( $image_id, array( 80, 60 ), true );

		return $output . '</div>';
	}

	/**
	 * The callback for the ajax call when the DFI changes
	 *
	 * @return void It's an ajax call.
	 */
	public function ajax_wrapper() {
		//phpcs:disable WordPress.Security.NonceVerification.Missing
		// This is only a preview, don't bother verifying.

		$dfi_id = filter_input( INPUT_POST, $this::OPTION, FILTER_VALIDATE_INT );
		if ( $dfi_id ) {
			echo $this->preview_image( $dfi_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		die(); // ajax call..
	}

	/**
	 * Add a settings link to the the plugin on the plugin page
	 *
	 * @param string[] $links An array of plugin action links.
	 *
	 * @return string[]
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
	 * @param string          $html              The post thumbnail HTML.
	 * @param int             $post_id           The post ID.
	 * @param int             $post_thumbnail_id The post thumbnail ID.
	 * @param string|int[]    $size              The post thumbnail size. Image size or array of width and height.
	 * @param string|string[] $attr              values (in that order). Default 'post-thumbnail'.
	 *
	 * @return string
	 */
	public function show_dfi( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		$dfi_id = $this->get_dfi_id();

		// If an image is set return that image.
		if ( $dfi_id !== (int) $post_thumbnail_id ) {
			return $html;
		}

		// Attributes can be a query string, parse that.
		if ( is_string( $attr ) ) {
			$str_attr = $attr;
			$attr     = array();
			wp_parse_str( $str_attr, $attr );
			unset( $str_attr );
		}
		/**
		 * After this the attr is aways an array.
		 *
		 * @var string[] $attr
		 */
		if ( isset( $attr['class'] ) ) {
			// There already are classes, we trust those.
			$attr['class'] .= ' default-featured-img';
		} else {
			// No classes in the attributes, try to get them form the HTML.
			$img = new \WP_HTML_Tag_Processor( $html );
			if ( $img->next_tag() ) {
				$attr['class'] = trim( $img->get_attribute( 'class' ) . ' default-featured-img' );
			}
		}

		$html = wp_get_attachment_image( $dfi_id, $size, false, $attr );
		return apply_filters( 'dfi_thumbnail_html', $html, $post_id, $dfi_id, $size, $attr );
	}
}
