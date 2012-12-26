=== Plugin Name ===
Contributors: janwoostendorp
Tags: media, image
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a default featured image to the media settings page

== Description ==

Add a default featured image to the media settings page. This featured image will show up if no featured image is set. Simple as that.

If you want to use a different image on certain occasions use the `dfi_thumbnail_id` filter like:

    <?php //this will work in your themes functions.php
    // Don't use a featured image on page 5
    add_action('template_redirect', function () {
			if ( is_single() ) {
					add_filter('dfi_thumbnail_id', function () { return 0; } );
			}
		});

    // use a different image on the "book" posttype, it's id is 12
    add_action('template_redirect', function () {
			if ( is_single() ) {
					add_filter('dfi_thumbnail_id', function () { return 12; } );
			}
		});
    ?>
= Suggestions are welcome =
 * Found a bug?
 * Want to help to translate it in your language?
 * Something to be improved?

[Contact me](http://wordpress.org/support/plugin/default-featured-image)

== Installation ==

1. Unzip the folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the settigns->media page and select an image.


== Frequently Asked Questions ==

= can I exclude a page or give it a different image? =

yes. you can exclude all kinds of things with the [conditional tags](http://codex.wordpress.org/Conditional_Tags).

    <?php //this will work in your themes functions.php
    // Dont use a featured image on page 5
    add_action('template_redirect', function () {
			if ( is_single() ) {
					add_filter('dfi_thumbnail_id', function () { return 0; } );
			}
		});

    // use a different image on the "book" posttype, it's id is 12
    add_action('template_redirect', function () {
			if ( is_single() ) {
					add_filter('dfi_thumbnail_id', function () { return 12; } );
			}
		});
    ?>

== Screenshots ==

1. The setting on the media page
2. The media manager will start with the current selected image

== Changelog ==

= 0.9 =
* Launch