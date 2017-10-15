=== Plugin Name ===
Contributors: janwoostendorp
Tags: media, image
Requires at least: 3.5
Tested up to: 4.8
Stable tag: 1.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a default featured image to the media settings page

== Description ==

Add a default featured image to the media settings page. This featured image will show up if no featured image is set. Simple as that.

For exceptions and to see which functions to use see the [FAQ](http://wordpress.org/extend/plugins/default-featured-image/faq/).

= Suggestions are welcome =
 * Found a bug?
 * Want to help to translate it in your language?
 * Something to be improved?

[Contact me](http://wordpress.org/support/plugin/default-featured-image)

== Installation ==

1. Unzip the folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the settings->media page and select an image.

== Frequently Asked Questions ==

= My chosen featured image doesn't show, why isn't it working? =

This plugin can't guarantee that it works. That depends on the themes. Still I want to know if it fails, so [contact me](http://wordpress.org/support/plugin/default-featured-image)

= Which functions can I use to display the featured image? =
The plugin uses the default WordPress functions `the_post_thumbnail` or `get_the_post_thumbnail`. `has_post_thumbnail` will always return true. `get_post_thumbnail_id` will return the ID set on the post or the DFI you set.

= Can I exclude a page or give it a different image? =
Yes you can by using the builtin `dfi_thumbnail_id` filter. It will give you the post id which you can use to check against.

**Don't use a featured image on page 23**

    function dfi_skip_page ( $dfi_id, $post_id ) {
      if ( $post_id == 23 ) {
        return 0; // invalid id
      }
      return $dfi_id; // the original featured image id
    }
    add_filter( 'dfi_thumbnail_id', 'dfi_skip_page', 10 , 2 );

**Use a different image for some categories**

The example below only works if the post has 'animals' as a category. Assigning just 'cats' won't work
To do that just don't nest the `if`

    function dfi_category ( $dfi_id, $post_id ) {
      // all which have 'animals' as a category
      if ( has_category( 'animals', $post_id ) ) {

        //sub category
        if ( has_category( 'cats', $post_id ) ) {
          return 7; // cats img
        } else if has_category( 'dogs', $post_id ) {
          return 8; // dogs img
        }

        return 6; // default animals picture
      }
      return $dfi_id; // the original featured image id
    }
    add_filter( 'dfi_thumbnail_id', 'dfi_category', 10, 2 );

**Different image for the post_type 'wiki'**

    function dfi_posttype_book ( $dfi_id, $post_id ) {
      $post = get_post($post_id);
      if ( 'wiki' === $post->post_type ) {
        return 31; // the image id
      }
      return $dfi_id; // the original featured image id
    }
    add_filter( 'dfi_thumbnail_id', 'dfi_posttype_book', 10, 2 );

= Can I change the HTML of the image returned? =
yes you can with the filter `dfi_thumbnail_html`.

	function dfi_add_class($html, $post_id, $default_thumbnail_id, $size, $attr) {
		// add a class to the existing class list
		$attr['class'] .= ' my-class';

		return wp_get_attachment_image( $default_thumbnail_id, $size, false, $attr );
	}
	add_filter( 'dfi_thumbnail_html', 'dfi_add_class', 10, 5 );

== Screenshots ==

1. The setting on the media page
2. The media manager will start with the current selected image

== Changelog ==

= 1.6.1 =
* Small readme.txt update.

= 1.6 =
* On of the last fixes didn't account for all situations.

= 1.5 =
* Fixed two small (and rare) warnings
* Added translation domain

= 1.4 =
* Added plugin images both the plugin header as the thumbnail. Based on the boat WP.org uses in it's theme previews
* Fixed a bug where the ajax calls didn't return the DFI [forum thread](https://wordpress.org/support/topic/dfi-woocommerce-facetwp?replies=10)

= 1.3 =
* Filter `dfi_thumbnail_id` now also returns the post ID of the post (or any postype) that is being called. See the FAQ for new examples

= 1.2 =
* Filter `dfi_thumbnail_id` is now called in an earlier stage.

= 1.1 =
* Fixed inheriting classes of the image

= 1.0 =
* Plugin will now remove it's setting on plugin removal
* added a default class to the `<img>` tag, if it shows a default featured image
* The default featured image will now also return with `get_post_thumbnail_id`, making the chance that it fail far far smaller.
* The image given in the media page is now validated

= 0.9 =
* Launch

== Upgrade Notice ==

= 1.0 =
Update makes sure that the set image will show. Everywhere.
