<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// &#8217;  == apostrophe
list( $display_version ) = explode( '-', bp_get_version() );
?>

<div class="bpa-panel">
	<h1><?php printf( 'What&#8217;s new in BuddyPress %s? A revamped @mentions interface, new profile field type, performance improvements, and many interesting things for developers.', $display_version ); ?></h1>
</div>

<div class="bpa-content">
	<h2>Revamped @mentions Interface</h2>
	<p>Forget the old days of trying to remember someone&#8217;s username when you want to @mention them in a conversation! With BuddyPress 2.1, type a <code>@</code> when leaving a status update or commenting on an activity item or blog post, and the new suggestions panel will open.</p>
</div>

<div class="bpa-content clearfix">
	<h2>Continuous Improvement</h2>

	<div class="bpa-content-third">
		<h3>New Profile Field Type: URL</h3>
		<p>Built to hold the address of another website, this new field type automatically creates a link to that site.</p>
	</div>

	<div class="bpa-content-third">
		<h3>Awesome Translations</h3>
		<p>BuddyPress supports high-quality translations that are automatically fetched by WordPress. Many thanks to our translation volunteers for making this possible.</p>
	</div>

	<div class="bpa-content-third">
		<h3>Performance Improvements</h3>
		<p>Like we do with every release, we&#8217ve made further optimizations to increase BuddyPress&#8217 performance and reduce its query overhead.</p>
	</div>
</div>

<div class="bpa-content">
	<h2>Enhancements for Plugin &amp; Theme Developers</h2>

	<div class="bpa-content-half">
		<p>If you&#8217;re a plugin developer, or make custom themes, or want to contribute back to the BuddyPress project, here&#8217s what you should know about this release:</p>
		<p>If you&#8217;ve used BuddyPress for a very long time, you might remember the <em>BuddyBar</em>; it was our toolbar before WordPress had its own toolbar. We started to deprecate it in BuddyPress 1.6. It is now formally deprecated, which means you should not use it for new sites.</p>
		<p>The classic <a href="https://github.com/buddypress/BP-Default">BP Default theme has moved to Github</a>. We moved it because BuddyPress development is now focused on our <a href="http://codex.buddypress.org/themes/theme-compatibility-1-7/a-quick-look-at-1-7-theme-compatibility/">theme compatibility</a> templates, which were introduced in BuddyPress 1.7. Don&#8217t worry, BP-Default is still bundled with BuddyPress releases.</p>
		<p>In BuddyPress 2.0, we added a new <code>BP_XProfile_Field_Type</code> API for managing profile field types. In this release, we&#8217ve added a new <code>bp_core_get_suggestions</code> API which powers our new @mentions interface. Both are cool, and are worth checking out.</p>
	</div>

	<div class="bpa-content-half">
		<p>Other interesting changes:</p>

		<ul>
			<li>In <code>BP_Group_Extension</code>, the <code>visibility</code> and <code>enable_nav_item</code> properties have been phased out in favor of new <code>access</code> and <code>show_tab</code> parameters.</li>
			<li>A new <code>group_activity</code> sort order has been added for Groups queries, to let you query for recently active members.</li>
			<li>Extra CSS classes have been added to Profile Field visibility field elements, allowing greater CSS customization.</li>
			<li>A <code>no_access_url</code> parameter has been added to <code>bp_core_new_subnav_item()</code>. This allows you to set the URL that users are redirected to when they do not have permission to access a sub-navigation item.</li>
			<li>When making searches with <code>BP_User_Query</code>, a new <code>search_wildcard</code> parameter gives you finer control over how the search SQL is constructed.</li>
			<li><a href="https://codex.buddypress.org/releases/version-2-1">&hellip;and lots more!</a></li>
		</ul>
	</div>
</div>
