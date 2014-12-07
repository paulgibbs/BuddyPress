<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// &#8217;  == apostrophe
list( $display_version ) = explode( '-', bp_get_version() );
?>

<div class="bpa-elevator-pitch">
	<h1><?php _e( 'Welcome! BuddyPress helps you run any kind of social network on your WordPress, with member profiles, activity streams, user groups, messaging, and&nbsp;more.', 'buddypress' ); ?></h1>
</div>

<article class="bpa-card">
	<h3>Getting You Started</h3>
	<p>We&#8217;ve activated our two most popular modules so you can start enjoying BuddyPress immediately; Activity Streams and Community Profiles.
	<p><strong>Activity Streams</strong> give your members a central place to discuss and comment on what other members are doing on your site. Activity Streams also provide Twitter-style @mentions and favouriting.</p>
	<p><strong>Extended Profiles</strong> are fully editadble profile field that allow you to define the fields that your members will use to describe themselves. Tailor profile fields to suit your audience.</p>
</article>

<div class="bpa-buttons">
	<div class="bpa-button-major bpa-button-activity">
		<a href="<?php echo esc_url( bp_get_admin_url( 'admin.php?page=bp-activity' ) ); ?>">
			<div class="bpa-button-major-icon"></div>
			<div class="bpa-button-major-contents">
				<h3><?php _ex( 'Activity Stream', 'dashboard screen button title', 'buddypress' ); ?></h3>
				<p><?php _ex( 'Dashboard Management', 'dashboard screen button title', 'buddypress' ); ?></p>
			</div>
		</a>
	</div>

	<div class="bpa-button-major bpa-button-xprofile">
		<a href="<?php echo esc_url( bp_get_admin_url( 'admin.php?page=bp-profile-setup' ) ); ?>">
			<div class="bpa-button-major-icon"></div>
			<div class="bpa-button-major-contents">
				<h3><?php _ex( 'Extended Profiles', 'dashboard screen button title', 'buddypress' ); ?></h3>
				<p><?php _ex( 'Create Custom Fields', 'dashboard screen button title', 'buddypress' ); ?></p>
			</div>
		</a>
	</div>
</div>

<div class="bpa-credits">
	<h4 class=""><?php _e( 'Project Leaders', 'buddypress' ); ?></h4>
	<ul class="bpa-people bpa-people-project-leaders">
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/johnjamesjacoby"><img src="//0.gravatar.com/avatar/81ec16063d89b162d55efe72165c105f?s=60" alt="John James Jacoby"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/johnjamesjacoby">John James Jacoby</a>
				<span><?php _e( 'Project Lead', 'buddypress' ); ?></span>
			</div>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/boonebgorges"><img src="//0.gravatar.com/avatar/9cf7c4541a582729a5fc7ae484786c0c?s=60" alt="Boone B. Gorges"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/boonebgorges">Boone B. Gorges</a>
				<span><?php _e( 'Lead Developer', 'buddypress' ); ?></span>
			</div>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/djpaul"><img src="//0.gravatar.com/avatar/3bc9ab796299d67ce83dceb9554f75df?s=60" alt="Paul Gibbs"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/djpaul">Paul Gibbs</a>
				<span><?php _e( 'Lead Developer', 'buddypress' ); ?></span>
			</div>
		</li>
	</ul>

	<h4 class=""><?php _e( 'Core Team', 'buddypress' ); ?></h4>
		<ul class="bpa-people bpa-people-core-team">
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/r-a-y"><img src="//0.gravatar.com/avatar/3bfa556a62b5bfac1012b6ba5f42ebfa?s=60" alt="Ray"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/r-a-y">Ray</a>
				<span><?php _e( 'Core Developer', 'buddypress' ); ?></span>
			</div>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/imath"><img src="//0.gravatar.com/avatar/8b208ca408dad63888253ee1800d6a03?s=60" alt="Mathieu Viet"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/imath">Mathieu Viet</a>
				<span><?php _e( 'Core Developer', 'buddypress' ); ?></span>
			</div>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/mercime"><img src="//0.gravatar.com/avatar/fae451be6708241627983570a1a1817a?s=60" alt="Mercime"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/mercime">Mercime</a>
				<span><?php _e( 'Navigator', 'buddypress' ); ?></span>
			</div>
		</li>
	</ul>

	<h4 class=""><?php _e( 'Recent Rockstars', 'buddypress' ); ?></h4>
	<ul class="bpa-people bpa-people-rockstars">
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/dcavins"><img src="//0.gravatar.com/avatar/a5fa7e83d59cb45ebb616235a176595a?s=60" alt="David Cavins"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/dcavins">David Cavins</a>
			</div>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/henry.wright"><img src="//0.gravatar.com/avatar/0da2f1a9340d6af196b870f6c107a248?s=60" alt="Henry Wright"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/henry.wright">Henry Wright</a>
			</div>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/danbp"><img src="//0.gravatar.com/avatar/0deae2e7003027fbf153500cd3fa5501?s=60" alt="danbp"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/danbp">danbp</a>
			</div>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/shanebp"><img src="//0.gravatar.com/avatar/ffd294ab5833ba14aaf175f9acc71cc4?s=60" alt="shanebp"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/shanebp">shanebp</a>
			</div
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/netweb"><img src="//0.gravatar.com/avatar/97e1620b501da675315ba7cfb740e80f?s=60" alt="Stephen Edgar"></a>
			<div class="bpa-person-details">
				<a href="http://profiles.wordpress.org/netweb">Stephen Edgar</a>
			</div>
		</li>
	</ul>

	<!--
	<h4 class=""><?php printf( __( 'Contributors to BuddyPress %s', 'buddypress' ), $display_version ); ?></h4>
	<p class="bpa-people-all-contributors">
		<a href="https://profiles.wordpress.org/adamt19/">adamt19</a>,
		<a href="https://profiles.wordpress.org/Viper007Bond/">Alex Mills (Viper007Bond)</a>,
		<a href="https://profiles.wordpress.org/allendav/">allendav</a>,
		<a href="https://profiles.wordpress.org/alternatekev/">alternatekev</a>,
		<a href="https://profiles.wordpress.org/automattic/">Automattic</a>,
		<a href="https://profiles.wordpress.org/beaulebens/">Beau Lebens (beaulebens)</a>,
		<a href="https://profiles.wordpress.org/boonebgorges/">Boone B Gorges (boonebgorges)</a>,
		<a href="https://profiles.wordpress.org/williamsba1/">Brad Williams (williamsba1)</a>,
		<a href="https://profiles.wordpress.org/sbrajesh/">Brajesh Singh (sbrajesh)</a>,
		<a href="https://profiles.wordpress.org/danbp/">danbp</a>,
		<a href="https://profiles.wordpress.org/dcavins/">David Cavins (dcavins)</a>,
		<a href="https://profiles.wordpress.org/ebellempire/">Erin B. (ebellempire)</a>,
		<a href="https://profiles.wordpress.org/esroyo/">esroyo</a>,
		<a href="https://profiles.wordpress.org/godavid33">godavid33</a>,
		<a href="http://profiles.wordpress.org/henry.wright">Henry Wright (henry.wright)</a>,
		<a href="https://profiles.wordpress.org/hnla/">Hugo (hnla)</a>,
		<a href="https://profiles.wordpress.org/imath/">Mathieu Viet (imath)</a>,
		<a href="https://profiles.wordpress.org/johnjamesjacoby/">John James Jacoby (johnjamesjacoby)</a>,
		<a href="https://profiles.wordpress.org/jconti/">Jose Conti (jconti)</a>,
		<a href="https://profiles.wordpress.org/jreeve/">jreeve</a>,
		<a href="https://profiles.wordpress.org/Offereins">Laurens Offereins (Offereins)</a>
		<a href="https://profiles.wordpress.org/lenasterg/">lenasterg</a>,
		<a href="https://profiles.wordpress.org/mercime/">mercime</a>,
		<a href="https://profiles.wordpress.org/tw2113/">Michael Beckwith (tw2113)</a>,
		<a href="https://profiles.wordpress.org/milesstewart88/">Miles Stewart (milesstewart88)</a>,
		<a href="https://profiles.wordpress.org/needle/">needle</a>,
		<a href="https://profiles.wordpress.org/sooskriszta/">OC2PS (sooskriszta)</a>,
		<a href="https://profiles.wordpress.org/DJPaul/">Paul Gibbs (DJPaul)</a>,
		<a href="https://profiles.wordpress.org/r-a-y/">r-a-y</a>,
		<a href="https://profiles.wordpress.org/rogercoathup/">Roger Coathup (rogercoathup)</a>,
		<a href="https://profiles.wordpress.org/pollyplummer/">Sarah Gooding (pollyplummer)</a>,
		<a href="https://profiles.wordpress.org/SGr33n/">Sergio De Falco (SGr33n)</a>,
		<a href="https://profiles.wordpress.org/shanebp/">shanebp</a>,
		<a href="https://profiles.wordpress.org/slaFFik/">Slava UA (slaFFik)</a>,
		<a href="https://profiles.wordpress.org/netweb/">Stephen Edgar (netweb)</a>,
		<a href="https://profiles.wordpress.org/karmatosed/">Tammie (karmatosed)</a>,
		<a href="https://profiles.wordpress.org/tomdxw/">tomdxw</a>,
		<a href="https://profiles.wordpress.org/treyhunner/">treyhunner</a>,
		<a href="https://profiles.wordpress.org/ubernaut/">ubernaut</a>,
		<a href="https://profiles.wordpress.org/wbajzek/">wbajzek</a>,
		<a href="https://profiles.wordpress.org/WCUADD/">WCUADD</a>,
		<a href="https://profiles.wordpress.org/wpdennis/">wpdennis</a>,
		<a href="https://profiles.wordpress.org/wolfhoundjesse/">wolfhoundjesse</a>.
	</p>

	<h4 class="wp-people-group"><?php _e( 'External Libraries', 'buddypress' ); ?></h4>
	<p class="wp-credits-list">
		<a href="https://github.com/ichord/At.js">At.js</a>,
		<a href="https://github.com/ichord/Caret.js">Caret.js</a>,
		<a href="https://github.com/carhartl/jquery-cookie">jquery.cookie</a>.
	</p>
	-->
</div>