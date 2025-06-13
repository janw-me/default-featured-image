<?php
// The export XML contains a lot of unnecessary data.
// Stripping it makes it smaller and faster.

$blueprint = file_get_contents( __DIR__ . '/blueprint.xml' );

// Image urls

$blueprint = preg_replace(
	'@<wp:attachment_url><!\[CDATA\[.*\/wp-content\/uploads\/\d+\/\d+\/(.*)\]\]><\/wp:attachment_url>@m',
	'<wp:attachment_url><![CDATA[https://raw.githubusercontent.com/janw-me/default-featured-image/main/.wordpress-org/blueprints/$1]]></wp:attachment_url>',
	$blueprint
);


/**
 * Delete lines.
 */
$deletes = array(
	'\s+' . '<[a-z-_:]+\><!\[CDATA\[\]\]><\/[a-z-_:]+\>', // Lines with empty CDATA tags.
	'<[a-z-_:]+\><!\[CDATA\[\]\]><\/[a-z-_:]+\>', // empty CDATA tags on lines with other tags.

	// TAGS, might be delted by value.
	'\s+' . '<wp:post_date>.*</wp:post_date>',
	'\s+' . '<wp:post_date_gmt>.*</wp:post_date_gmt>',
	'\s+' . '<wp:post_modified>.*</wp:post_modified>',
	'\s+' . '<wp:post_modified_gmt>.*</wp:post_modified_gmt>',
	'\s+' . '<wp:comment_status>.*</wp:comment_status>',
	'\s+' . '<wp:ping_status>.*</wp:ping_status>',
	'\s+' . '<wp:post_parent>0</wp:post_parent>',
	'\s+' . '<wp:menu_order>0</wp:menu_order>',
	'\s+' . '<wp:is_sticky>0</wp:is_sticky>',
	'\s+' . '<description></description>',
	'\s+' . '<category domain="category" nicename="uncategorized"><!\[CDATA\[Uncategorized\]\]></category>',

	// Opening comments.
	'^<!-- .* -->\n',
);

// Replace the lines in the blueprint
foreach ( $deletes as $line ) {
	$blueprint = preg_replace( '@' . $line . '@m', '', $blueprint );
}


// Write file.
file_put_contents( __DIR__ . '/blueprint.xml', $blueprint );
