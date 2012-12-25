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

If you want to use a diffrent image on certain occasions use the `dfi_thumbnail_id` filter like:

    <?php //this will work in your themes funcitons.php
    // Dont use a featured image on page 5
    add_action('template_redirect', function () {
			if ( is_single() ) {
					add_filter('dfi_thumbnail_id', function () { return 0; } );
			}
		});

    // use a diffrent image on the "book" posttype, it's id is 12
    add_action('template_redirect', function () {
			if ( is_single() ) {
					add_filter('dfi_thumbnail_id', function () { return 12; } );
			}
		});
    ?>

== Installation ==

1. Unzip the folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the settigns->media page and select an image.


== Frequently Asked Questions ==

= can I exclude a page or give it a diffrent image? =

yes. you can exclude all kinds of things with the [conditional tags](http://codex.wordpress.org/Conditional_Tags)

    <?php //this will work in your themes funcitons.php
    // Dont use a featured image on page 5
    add_action('template_redirect', function () {
			if ( is_single() ) {
					add_filter('dfi_thumbnail_id', function () { return 0; } );
			}
		});

    // use a diffrent image on the "book" posttype, it's id is 12
    add_action('template_redirect', function () {
			if ( is_single() ) {
					add_filter('dfi_thumbnail_id', function () { return 12; } );
			}
		});
    ?>

== Screenshots ==

@todo

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.9 =
* Launch