<?php
/**
 * An extension to the WordPress plugin "Default Featured Image" that uses the default featured image set for post
 * featured images that can't be loaded via HTTPS. This option is non-render blocking.
 *
 * WARNING: This feature is a simple proof of concept and should be better integrated if you plan to use it in a
 * production environment (e.g., checking for result in cache to avoid constant `fetch()` requests, even though
 * they're non-render blocking).
 *
 * @return void
 * @author Kolja Nolte <kolja.nolte@gmail.com>
 */
add_action(
	'wp_head',
	function (): void {
		// Get the plugin's set default featured image ID from the WordPress options.
		$default_featured_image_id = get_option( 'dfi_image_id' );

		// If no default featured image ID is set, exit the function.
		if ( ! $default_featured_image_id ) {
			return;
		}

		// Get the URL and srcset of the default featured image.
		$default_featured_image_url    = wp_get_attachment_url( $default_featured_image_id );
		$default_featured_image_srcset = wp_get_attachment_image_srcset( $default_featured_image_id );
		?>
		<script>
          /**
           * Adds an event listener to the DOMContentLoaded event to wait until all default featured images are loaded.
           */
          document.addEventListener('DOMContentLoaded', function () {
            // Select all images with the class 'wp-post-image'.
            const images = document.querySelectorAll('img.wp-post-image')

            // Store the default featured image URL and srcset.
            const defaultFeaturedImage  = '<?php echo esc_js( $default_featured_image_url ); ?>'
            const defaultFeaturedSrcset = '<?php echo esc_js( $default_featured_image_srcset ); ?>'

            // Loop through each image.
            for (const image of images) {
              // If the image does not have the class 'default-featured-img', check its source.
              if (!image.classList.contains('default-featured-img')) {
                // Fetch the image source to check if it is valid.
                fetch(image.getAttribute('src'), { method: 'HEAD' })
                .then(response => {
                  // If the response is not OK or the content type is not an image, set the default featured image.
                  if (!response.ok || !response.headers.get('content-type').includes('image')) {
                    image.setAttribute('src', defaultFeaturedImage)
                    image.setAttribute('srcset', defaultFeaturedSrcset)
                  }
                })
                .catch(error => {
                  // Log any errors and set the default featured image.
                  console.error('Error fetching image:', error)
                })
              }
            }
          })
		</script>
		<?php
	}
);
