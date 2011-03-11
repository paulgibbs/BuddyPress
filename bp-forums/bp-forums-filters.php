<?php

/* Apply WordPress defined filters */
add_filter( 'bp_forums_bbconfig_location', 'wp_filter_kses', 1 );
add_filter( 'bp_forums_bbconfig_location', 'esc_attr', 1 );

add_filter( 'bp_get_the_topic_title', 'wp_filter_kses', 1 );
add_filter( 'bp_get_the_topic_latest_post_excerpt', 'bp_forums_filter_kses', 1 );
add_filter( 'bp_get_the_topic_post_content', 'bp_forums_filter_kses', 1 );

add_filter( 'bp_get_the_topic_title', 'force_balance_tags' );
add_filter( 'bp_get_the_topic_latest_post_excerpt', 'force_balance_tags' );
add_filter( 'bp_get_the_topic_post_content', 'force_balance_tags' );

add_filter( 'bp_get_the_topic_title', 'wptexturize' );
add_filter( 'bp_get_the_topic_poster_name', 'wptexturize' );
add_filter( 'bp_get_the_topic_last_poster_name', 'wptexturize' );
add_filter( 'bp_get_the_topic_post_content', 'wptexturize' );
add_filter( 'bp_get_the_topic_post_poster_name', 'wptexturize' );

add_filter( 'bp_get_the_topic_title', 'convert_smilies' );
add_filter( 'bp_get_the_topic_latest_post_excerpt', 'convert_smilies' );
add_filter( 'bp_get_the_topic_post_content', 'convert_smilies' );

add_filter( 'bp_get_the_topic_title', 'convert_chars' );
add_filter( 'bp_get_the_topic_latest_post_excerpt', 'convert_chars' );
add_filter( 'bp_get_the_topic_post_content', 'convert_chars' );

add_filter( 'bp_get_the_topic_post_content', 'wpautop' );
add_filter( 'bp_get_the_topic_latest_post_excerpt', 'wpautop' );

add_filter( 'bp_get_the_topic_post_content', 'stripslashes_deep' );
add_filter( 'bp_get_the_topic_title', 'stripslashes_deep' );
add_filter( 'bp_get_the_topic_latest_post_excerpt', 'stripslashes_deep' );
add_filter( 'bp_get_the_topic_poster_name', 'stripslashes_deep' );
add_filter( 'bp_get_the_topic_last_poster_name', 'stripslashes_deep' );
add_filter( 'bp_get_the_topic_object_name', 'stripslashes_deep' );

add_filter( 'bp_get_the_topic_post_content', 'make_clickable' );

add_filter( 'bp_get_forum_topic_count_for_user', 'bp_core_number_format' );
add_filter( 'bp_get_forum_topic_count', 'bp_core_number_format' );

add_filter( 'bp_get_the_topic_title', 'bp_forums_make_nofollow_filter' );
add_filter( 'bp_get_the_topic_latest_post_excerpt', 'bp_forums_make_nofollow_filter' );
add_filter( 'bp_get_the_topic_post_content', 'bp_forums_make_nofollow_filter' );

function bp_forums_filter_kses( $content ) {
	global $allowedtags;

	$forums_allowedtags = $allowedtags;
	$forums_allowedtags['span'] = array();
	$forums_allowedtags['span']['class'] = array();
	$forums_allowedtags['div'] = array();
	$forums_allowedtags['div']['class'] = array();
	$forums_allowedtags['div']['id'] = array();
	$forums_allowedtags['a']['class'] = array();
	$forums_allowedtags['img'] = array();
	$forums_allowedtags['br'] = array();
	$forums_allowedtags['p'] = array();
	$forums_allowedtags['img']['src'] = array();
	$forums_allowedtags['img']['alt'] = array();
	$forums_allowedtags['img']['class'] = array();
	$forums_allowedtags['img']['width'] = array();
	$forums_allowedtags['img']['height'] = array();
	$forums_allowedtags['img']['class'] = array();
	$forums_allowedtags['img']['id'] = array();
	$forums_allowedtags['code'] = array();
	$forums_allowedtags['blockquote'] = array();

	$forums_allowedtags = apply_filters( 'bp_forums_allowed_tags', $forums_allowedtags );
	return wp_kses( $content, $forums_allowedtags );
}

function bp_forums_filter_tag_link( $link, $tag, $page, $context ) {
	global $bp;

	return apply_filters( 'bp_forums_filter_tag_link', bp_get_root_domain() . '/' . $bp->forums->slug . '/tag/' . $tag . '/' );
}
add_filter( 'bb_get_tag_link', 'bp_forums_filter_tag_link', 10, 4);

function bp_forums_make_nofollow_filter( $text ) {
	return preg_replace_callback( '|<a (.+?)>|i', 'bp_forums_make_nofollow_filter_callback', $text );
}
	function bp_forums_make_nofollow_filter_callback( $matches ) {
		$text = $matches[1];
		$text = str_replace( array( ' rel="nofollow"', " rel='nofollow'"), '', $text );
		return "<a $text rel=\"nofollow\">";
	}

/**
 * bp_forums_add_forum_topic_to_page_title( $title )
 *
 * Append forum topic to page title
 *
 * @global object $bp
 * @param string $title
 * @return string
 */
function bp_forums_add_forum_topic_to_page_title( $title ) {
	global $bp;

	if ( $bp->current_action == 'forum' && !empty( $bp->action_variables[0] ) && 'topic' == $bp->action_variables[0] )
		if ( bp_has_forum_topic_posts() )
			$title .= ' &#124; ' . bp_get_the_topic_title();

	return $title;
}
add_filter( 'bp_page_title', 'bp_forums_add_forum_topic_to_page_title' );

/**
 * bp_forums_strip_mentions_on_post_edit( $title )
 *
 * Removes the anchor tag autogenerated for at-mentions when forum topics and posts are edited.
 * Prevents embedded anchor tags.
 *
 * @global object $bp
 * @param string $content
 * @return string $content
 */
function bp_forums_strip_mentions_on_post_edit( $content ) {
	global $bp;

	$content = htmlspecialchars_decode( $content );

	$pattern = "|<a href=&#039;" . bp_get_root_domain() . "/" . bp_get_members_root_slug() . "/[A-Za-z0-9-_\.]+/&#039; rel=&#039;nofollow&#039;>(@[A-Za-z0-9-_\.@]+)</a>|";

	$content = preg_replace( $pattern, "$1", $content );

	return $content;
}
add_filter( 'bp_get_the_topic_post_edit_text', 'bp_forums_strip_mentions_on_post_edit' );
add_filter( 'bp_get_the_topic_text', 'bp_forums_strip_mentions_on_post_edit' );
?>