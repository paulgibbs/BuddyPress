<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// &#8217;  == apostrophe
list( $display_version ) = explode( '-', bp_get_version() );
?>

<div class="bpa-panel">
	<h1><?php printf( __( 'Welcome to BuddyPress %s', 'buddypress' ), $display_version ); ?></h1>
</div>

<div class="bpa-content">
	<h2><?php _e( 'For Newcomers', 'buddypress' ); ?></h2>
	<p><?php _e( 'The best way to start enjoying BuddyPress is to switch on only a few of its features (or &ldquo;modules&rdquo;) until your community finds its feet. The two most popular modules are <strong>Activity Streams</strong> and <strong>Profile Fields</strong>, so they&#8217;ve already been activated for you. Between them, they provide key features that are common among modern social networking sites.', 'buddypress' ); ?></p>
	<p><?php _e( 'Activity Streams provide a central place for your community to share, discuss, and comment; it also provides Twitter-style @mentions and favoriting. The Profile Fields module allows you to define and manage the available profile fields that members of your community can use to describe themselves.', 'buddypress' ); ?></p>
	<p><?php
	printf(
		__( 'To learn more about these modules and everything else in BuddyPress, go to the <a href="%s">Modules screen</a> and consult the <a href="%s">documentation</a>.', 'buddypress' ),
		esc_url( bp_get_admin_url( 'admin.php?page=bp-components' ) ),
		esc_url( 'https://codex.buddypress.org/buddypress-components-and-features/' )
	);
	?></p>

	<h2><?php _e( 'For Old Hands', 'buddypress' ); ?></h2>
	<p><?php _e( 'Welcome to the new-look BuddyPress Dashboard. While everything remains in the same place as before, this represents the first step towards a refreshed and mobile-friendly admin interface for BuddyPress.', 'buddypress' ); ?></p>
	<p><?php printf(
		__( 'Learn about the changes in this release of BuddyPress on the <a href="#">&ldquo;What&#8217;s new?&rdquo;</a> screen.', 'buddypress' ),
		esc_url( bp_get_admin_url( 'admin.php?page=bp-changelog' ) )
	);
	?></p>
</article>

<!--<div class="bpa-buttons">
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
				<h3><?php _ex( 'Profile Fields', 'dashboard screen button title', 'buddypress' ); ?></h3>
				<p><?php _ex( 'Create &amp; Manage', 'dashboard screen button title', 'buddypress' ); ?></p>
			</div>
		</a>
	</div>
</div>-->

<!--
<div class="bpa-credits">
	<h3><?php _e( 'Core Team', 'buddypress' ); ?></h3>
	<ul class="bpa-people bpa-people-project-leaders">
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/johnjamesjacoby"><img src="//0.gravatar.com/avatar/81ec16063d89b162d55efe72165c105f?s=60" alt="John James Jacoby"></a>
			<a href="http://profiles.wordpress.org/johnjamesjacoby">John James Jacoby</a>
			<span><?php _e( 'Project Lead', 'buddypress' ); ?></span>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/boonebgorges"><img src="//0.gravatar.com/avatar/9cf7c4541a582729a5fc7ae484786c0c?s=60" alt="Boone B. Gorges"></a>
			<a href="http://profiles.wordpress.org/boonebgorges">Boone B. Gorges</a>
			<span><?php _e( 'Lead Developer', 'buddypress' ); ?></span>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/djpaul"><img src="//0.gravatar.com/avatar/3bc9ab796299d67ce83dceb9554f75df?s=60" alt="Paul Gibbs"></a>
			<a href="http://profiles.wordpress.org/djpaul">Paul Gibbs</a>
			<span><?php _e( 'Lead Developer', 'buddypress' ); ?></span>
		</li>
	</ul>
	<br>

	<ul class="bpa-people bpa-people-core-team">
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/r-a-y"><img src="//0.gravatar.com/avatar/3bfa556a62b5bfac1012b6ba5f42ebfa?s=60" alt="Ray"></a>
			<a href="http://profiles.wordpress.org/r-a-y">Ray</a>
			<span><?php _e( 'Core Developer', 'buddypress' ); ?></span>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/imath"><img src="//0.gravatar.com/avatar/8b208ca408dad63888253ee1800d6a03?s=60" alt="Mathieu Viet"></a>
			<a href="http://profiles.wordpress.org/imath">Mathieu Viet</a>
			<span><?php _e( 'Core Developer', 'buddypress' ); ?></span>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/mercime"><img src="//0.gravatar.com/avatar/fae451be6708241627983570a1a1817a?s=60" alt="Mercime"></a>
			<a href="http://profiles.wordpress.org/mercime">Mercime</a>
			<span><?php _e( 'Navigator', 'buddypress' ); ?></span>
		</li>
	</ul>

	<h3><?php _e( 'Recent Rockstars', 'buddypress' ); ?></h3>
	<ul class="bpa-people bpa-people-rockstars">
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/dcavins"><img src="//0.gravatar.com/avatar/a5fa7e83d59cb45ebb616235a176595a?s=60" alt="David Cavins"></a>
			<a href="http://profiles.wordpress.org/dcavins">David Cavins</a>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/henry.wright"><img src="//0.gravatar.com/avatar/0da2f1a9340d6af196b870f6c107a248?s=60" alt="Henry Wright"></a>
			<a href="http://profiles.wordpress.org/henry.wright">Henry Wright</a>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/danbp"><img src="//0.gravatar.com/avatar/0deae2e7003027fbf153500cd3fa5501?s=60" alt="danbp"></a>
			<a href="http://profiles.wordpress.org/danbp">danbp</a>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/shanebp"><img src="//0.gravatar.com/avatar/ffd294ab5833ba14aaf175f9acc71cc4?s=60" alt="shanebp"></a>
			<a href="http://profiles.wordpress.org/shanebp">shanebp</a>
		</li>
		<li class="bpa-person">
			<a href="http://profiles.wordpress.org/netweb"><img src="//0.gravatar.com/avatar/97e1620b501da675315ba7cfb740e80f?s=60" alt="Stephen Edgar"></a>
			<a href="http://profiles.wordpress.org/netweb">Stephen Edgar</a>
		</li>
	</ul>

	<h3><?php printf( __( 'Contributors to BuddyPress %s', 'buddypress' ), $display_version ); ?></h3>
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

	<h3><?php _e( 'External Libraries', 'buddypress' ); ?></h3>
	<p class="wp-credits-list">
		<a href="https://github.com/ichord/At.js">At.js</a>,
		<a href="https://github.com/ichord/Caret.js">Caret.js</a>,
		<a href="https://github.com/carhartl/jquery-cookie">jquery.cookie</a>.
	</p>
	-->
</div>