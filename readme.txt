=== Plugin Name ===
Contributors: janwoostendorp
Tags: media, image
Requires at least: 3.5
Tested up to: 3.8
Stable tag: 1.1
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

yes. you can exclude all kinds of things with the [conditional tags](http://codex.wordpress.org/Conditional_Tags). A few examples which you can paste in your `functions.php`

**Dont use a featured image on page 5**

		function dfi_skip_page( $dfi_id ) {
			if ( is_single( 5 ) || get_the_ID() == 5 ) {
				return 0; // invalid id
			}
			return $dfi_id; // the original featured image id
		}
		add_filter('dfi_thumbnail_id', 'dfi_skip_page' );


**Use a different image on the "book" posttype. The ID of the image is 12**

		function dfi_posttype_book( $dfi_id ) {
			if ( is_singular( 'book' ) || get_post_type() == 'book' ) {
				return 12; // the image id
			}
			return $dfi_id; // the original featured image id
		}
		add_filter('dfi_thumbnail_id', 'dfi_posttype_book' );

**Use a different image on certain categories**

		function dfi_category( $dfi_id ) {
			if ( has_category( 'category-slug' ) ) {
				return 13; // the image id
			} else if ( has_category( 'other_category' ) ) {
				return 14; // the image id
			}
			return $dfi_id; // the original featured image id
		}
		add_filter('dfi_thumbnail_id', 'dfi_category' );

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

= 0.9 =
* Launch

= 1.0 =
* Plugin will now remove it's setting on plugin removal
* added a default class to the `<img>` tag, if it shows a default featured image
* The default featured image will now also return with `get_post_thumbnail_id`, making the chance that it fail far far smaller.
* The image given in the media page is now validated

= 1.1 =
* Fixed inheriting classes of the image

== Upgrade Notice ==

= 1.0 =
Update makes sure that the set image will show. Everywhere.