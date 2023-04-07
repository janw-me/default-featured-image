=== Plugin Name ===
Contributors: janwoostendorp
Tags: media, image
Requires at least: 3.5
Tested up to: 6.2
Requires PHP: 7.4
Stable tag: 1.7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a Default Featured Image for all posts & pages.

== Description ==

Add a default featured image to the media settings page. This featured image will show up if no featured image is set. Simple as that.

Take a look at [FAQ](http://wordpress.org/extend/plugins/default-featured-image/faq/) for the basic questions.
Feel free to contact me [on the forum](https://wordpress.org/support/plugin/default-featured-image/) or on the [github repository](https://github.com/janw-me/default-featured-image).

== Installation ==

1. Unzip the folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the settings->media page and select an image.

== Frequently Asked Questions ==

= My chosen featured image doesn't show, why isn't it working? =

This plugin works out of the box for most cases, but not always. If it doesn't work you can try the following things.

 - Switch themes. Most of the time the theme does something weird.
 - Are you using the Core WordPress functions to get the image? (see the FAQ below this one).
 - Do normal feature images work?
 - Might it be hidden via css? DFI images have an extra `default-featured-img` class added to them.

Still having problems? I want to know if it fails, so [contact me](http://wordpress.org/support/plugin/default-featured-image)

= Which functions can I use to display the featured image? =
There are no new functions, all core WordPress functions can be used.

 - [the_post_thumbnail](https://developer.wordpress.org/reference/functions/the_post_thumbnail/) / [get_the_post_thumbnail](https://developer.wordpress.org/reference/functions/get_the_post_thumbnail/) Display the image.
 - [the_post_thumbnail_url](https://developer.wordpress.org/reference/functions/the_post_thumbnail_url/) / [get_the_post_thumbnail_url](https://developer.wordpress.org/reference/functions/get_the_post_thumbnail_url/) Get the url.
 - [has_post_thumbnail](https://developer.wordpress.org/reference/functions/has_post_thumbnail/) If a DFI is set it will always return true.
 - [get_post_thumbnail_id](https://developer.wordpress.org/reference/functions/get_post_thumbnail_id/) will return the ID set on the post or the DFI.

= Can I set a different image for a custom post type?
Yes, the following code will set a different image.

    add_filter( 'dfi_thumbnail_id', 'dfi_posttype_book', 10, 2 );
    function dfi_posttype_book( $dfi_id, $post_id ) {
        $post = get_post( $post_id );
        if ( 'book' === $post->post_type ) {
            return 31; // the image id for the book post type.
        }

        return $dfi_id; // the original featured image id.
    }

= Can I set different images per category? =
Yes, the following snippet will set different images based on the category.

    add_filter( 'dfi_thumbnail_id', 'dfi_category', 10, 2 );
    function dfi_category( $dfi_id, $post_id ) {
        // Set a different image for posts that have the 'cats' category set.
        // This will trigger first, if multiple categories have been set.
        if ( has_category( 'cats', $post_id ) ) {
            return 7; // cats img id.
        }
        // Set a different image for posts that have the 'cats' category set.
        if ( has_category( 'dogs', $post_id ) ) {
            return 8; // dogs img id.
        }

        return $dfi_id; // the original featured image id.
    }


= Can I change the HTML of the default featured image? =
When a Default Featured Image is used it will already add an extra class `default-featured-img`.
This can be used for styling.

If you need more you can change the whole HTML with the filter `dfi_thumbnail_html`.

    add_filter( 'dfi_thumbnail_html', 'dfi_add_class', 10, 5 );
    function dfi_add_class( $html, $post_id, $default_thumbnail_id, $size, $attr ) {
        // Add a class to the existing class list.
        $attr['class'] .= ' my-class';

        return wp_get_attachment_image( $default_thumbnail_id, $size, false, $attr );
    }

= Can I exclude one page from having a Default Featured Image? =
The following code will exclude the post/page with ID 23.

    add_filter( 'dfi_thumbnail_id', 'dfi_skip_page', 10, 2 );
    function dfi_skip_page( $dfi_id, $post_id ) {
        if ( $post_id == 23 ) {
            return 0; // invalid id.
        }

        return $dfi_id; // the original featured image id.
    }

== Screenshots ==

1. The setting on the media page
2. The media manager will start with the current selected image

== Changelog ==
= 1.7.3 =
* PHP 7.4 and WP 6.2 are now required. This is to use the new [WP_HTML_Tag_Processor](https://make.wordpress.org/core/2023/03/07/introducing-the-html-api-in-wordpress-6-2/) functions.
* Fixed a bug where classes were overridden.

= 1.7.2.1 =
* Development is now done in git.

= 1.7.2 =
* Added extra context to a translation as suggested by [Alex Lion](https://wordpress.org/support/topic/i18n-issue-14/)

= 1.7.1 =
* Fixed weird SVN deployment bug.

= 1.7.0 =
* moved main class to it's own file.
* Added second class that can hold exceptions with other plugins
* The first exception is for WP User Frontend
* The second exception  is for WP All Import.

= 1.6.4 =
* `get_post_meta($post_id)` without specifying the meta_key didn't find the DFI. It will now even use an even deeper level and set it in the core cache.

= 1.6.3 =
* Fixed plugin header which blocked installing it.

= 1.6.2 =
* Plugin now follows WP coding standard
* Fixed a small bug where DFI overrides attachments featured images. mp3 has a music note by default, DFI should not override that.

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
