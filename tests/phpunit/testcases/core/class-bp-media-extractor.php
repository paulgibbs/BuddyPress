<?php
/**
 * @group core
 * @group BP_Media_Extractor
 */
class BP_Tests_Media_Extractor extends BP_UnitTestCase {
	public static $media_extractor      = null;
	public static $post_media_extractor = null;
	public static $richtext             = '';


	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$media_extractor      = new BP_Media_Extractor();
		self::$post_media_extractor = new BP_Media_Extractor_Post();

		self::$richtext = "Hello world.

		This sample text is used to test the media extractor parsing class. @paulgibbs thinks it's pretty cool.
		Another thing really cool is this @youtube:

		https://www.youtube.com/watch?v=2mjvfnUAfyo

		This video is literally out of the world, but uses a different protocol to the embed above:

		http://www.youtube.com/watch?v=KaOC9danxNo

		<a href='https://example.com'>Testing a regular link.</a>
		<strong>But we should throw in some markup and maybe even an <img src='http://example.com/image.gif'>.
		<a href='http://example.com'><img src='http://example.com/image-in-a-link.gif' /></a></strong>.
		It definitely does not like <img src='data:1234567890A'>data URIs</img>. @

		The parser only extracts wp_allowed_protocols() protocols, not something like <a href='phone:004400'>phone</a>.

		[caption]Here is a caption shortcode.[/caption]

		There are two types of [gallery] shortcodes; one like that, and another with IDs specified.

		Audio shortcodes: [audio src='source.mp3'] [audio src='source.mp3' loop='on' autoplay='off' preload='metadata'].
		";
	}

	public function setUp() {
		parent::setUp();

		$this->factory->user->create( array( 'user_login' => 'paulgibbs' ) );
	}

	public function tearDown() {
		parent::tearDown();

		$this->remove_added_uploads();
	}


	/**
	 * General.
	 */

	public function test_check_media_extraction_return_types() {
		$media = self::$media_extractor->extract( self::$richtext );

		foreach ( array( 'has', 'embeds', 'images', 'links', 'mentions', 'shortcodes' ) as $key ) {
			$this->assertArrayHasKey( $key, $media );
			$this->assertInternalType( 'array', $media[ $key ] );
		}

		foreach ( $media['has'] as $item ) {
			$this->assertInternalType( 'int', $item );
		}

		foreach ( $media['links'] as $item ) {
			$this->assertArrayHasKey( 'url', $item );
			$this->assertInternalType( 'string', $item['url'] );
			$this->assertNotEmpty( $item['url'] );
		}

		foreach ( $media['mentions'] as $item ) {
			$this->assertArrayHasKey( 'name', $item );
			$this->assertInternalType( 'string', $item['name'] );
			$this->assertNotEmpty( $item['name'] );
		}

		foreach ( $media['images'] as $item ) {
			$this->assertArrayHasKey( 'height', $item );
			$this->assertInternalType( 'int', $item['height'] );

			$this->assertArrayHasKey( 'width', $item );
			$this->assertInternalType( 'int', $item['width'] );

			$this->assertArrayHasKey( 'source', $item );
			$this->assertInternalType( 'string', $item['source'] );
			$this->assertNotEmpty( $item['source'] );

			$this->assertArrayHasKey( 'url', $item );
			$this->assertInternalType( 'string', $item['url'] );
			$this->assertNotEmpty( $item['url'] );
		}

		foreach ( $media['shortcodes'] as $shortcode_type => $item ) {
			$this->assertArrayHasKey( 'count', $item );
			$this->assertInternalType( 'int', $item['count'] );
		}

		foreach ( $media['embeds'] as $item ) {
			$this->assertArrayHasKey( 'url', $item );
			$this->assertInternalType( 'string', $item['url'] );
			$this->assertNotEmpty( $item['url'] );
		}

		//djpaultodo - audio
	}

	public function test_check_media_extraction_counts_are_correct() {
		$media = self::$media_extractor->extract( self::$richtext );

		foreach ( $media['has'] as $type => $total ) {
			$this->assertTrue( count( $media[ $type ] ) === $total, "Difference with the 'has' count for {$type}." );
		}
	}


	public function test_extract_multiple_media_types_from_content() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::LINKS | BP_Media_Extractor::MENTIONS );

		$this->assertNotEmpty( $media['links'] );
		$this->assertNotEmpty( $media['mentions'] );
		$this->assertArrayNotHasKey( 'shortcodes', $media );
	}


	/**
	 * Link extraction.
	 */

	public function test_extract_links_from_content() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::LINKS );

		$this->assertArrayHasKey( 'links', $media );
		$this->assertSame( 'https://example.com', $media['links'][0]['url'] );
		$this->assertSame( 'http://example.com',  $media['links'][1]['url'] );
	}

	public function test_extract_no_links_from_content_with_invalid_links() {
		$richtext = "This is some sample text, with links, but not the kinds we want.		
		<a href=''>Empty links should be ignore<a/> and
		<a href='phone:004400'>weird protocols should be ignored, too</a>.
		";

		$media = self::$media_extractor->extract( $richtext, BP_Media_Extractor::LINKS );
		$this->assertSame( 0, $media['has']['links'] );
	}


	/**
	 * at-mentions extraction.
	 */

	public function test_extract_mentions_from_content_with_activity_enabled() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::MENTIONS );

		$this->assertArrayHasKey( 'user_id', $media['mentions'][0] );
		$this->assertSame( 'paulgibbs', $media['mentions'][0]['name'] );
	}

	public function test_extract_mentions_from_content_with_activity_disabled() {
		$was_activity_enabled = false;

		// Turn activity off.
		if ( isset( buddypress()->active_components['activity'] ) ) {
			unset( buddypress()->active_components['activity'] );
			$was_activity_enabled = true;
		}


		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::MENTIONS );

		$this->assertArrayNotHasKey( 'user_id', $media['mentions'][0] );
		$this->assertSame( 'paulgibbs', $media['mentions'][0]['name'] );


		// Turn activity on.
		if ( $was_activity_enabled ) {
			buddypress()->active_components['activity'] = 1;
		}
	}


	/**
	 * Shortcodes extraction.
	 */

	public function test_extract_shortcodes_from_content() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::SHORTCODES );

		$this->assertArrayHasKey( 'shortcodes', $media );
		$this->assertSame( 1, $media['shortcodes']['caption']['count'] );
		$this->assertSame( 1, $media['shortcodes']['gallery']['count'] );
	}

	public function test_extract_no_shortcodes_from_content_with_unregistered_shortcodes() {
		$richtext = 'This sammple text has some made-up [fake]shortcodes[/fake].';

		$media = self::$media_extractor->extract( $richtext, BP_Media_Extractor::SHORTCODES );
		$this->assertSame( 0, $media['has']['shortcodes'] );
	}


	/**
	 * oEmbeds extraction.
	 */

	public function test_extract_oembeds_from_content() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::EMBEDS );

		$this->assertArrayHasKey( 'embeds', $media );
		$this->assertSame( 'https://www.youtube.com/watch?v=2mjvfnUAfyo', $media['embeds'][0]['url'] );
		$this->assertSame( 'http://www.youtube.com/watch?v=KaOC9danxNo',  $media['embeds'][1]['url'] );
	}


	/**
	 * Images extraction (src tags).
	 */

	// both quote styles
	public function test_extract_images_from_content_with_src_tags() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::IMAGES );

		$this->assertArrayHasKey( 'images', $media );
		$media = array_values( wp_list_filter( $media['images'], array( 'source' => 'html' ) ) );
	
		$this->assertSame( 'http://example.com/image.gif',           $media[0]['url'] );
		$this->assertSame( 'http://example.com/image-in-a-link.gif', $media[1]['url'] );
	}

	// empty src attributes, data: URIs
	public function test_extract_no_images_from_content_with_invalid_src_tags() {
		$richtext = 'This sample text will contain images with invalid src tags, like this:
		<img src="data://abcd"> or <img src="phone://0123" />.
		';

		$media = self::$media_extractor->extract( $richtext, BP_Media_Extractor::IMAGES );

		$this->assertArrayHasKey( 'images', $media );
		$this->assertSame( 0, $media['has']['images'] );
	}


	/**
	 * Images extraction (galleries).
	 */

	public function test_extract_images_from_content_with_galleries_variant_no_ids() {
		// To test the [gallery] shortcode, we need to create a post and an attachment.
		$post_id       = $this->factory->post->create( array( 'post_content' => self::$richtext ) );
		$attachment_id = $this->factory->attachment->create_object( 'image.jpg', $post_id, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment'
		) );
		wp_update_attachment_metadata( $attachment_id, array( 'width' => 100, 'height' => 100 ) );


		// Extract the gallery images.
		$media = self::$post_media_extractor->extract( self::$richtext, BP_Media_Extractor::IMAGES, array(
			'post' => get_post( $post_id ),
		) );

		$this->assertArrayHasKey( 'images', $media );
		$media = array_values( wp_list_filter( $media['images'], array( 'source' => 'galleries' ) ) );
		$this->assertCount( 1, $media );

		$this->assertSame( 'http://' . WP_TESTS_DOMAIN . '/wp-content/uploads/image.jpg', $media[0]['url'] );
	}

	public function test_extract_images_from_content_with_galleries_variant_ids() {
		// To test the [gallery] shortcode, we need to create a post and attachments.
		$attachment_ids = array();
		foreach ( range( 1, 3 ) as $i ) {
			$attachment_id = $this->factory->attachment->create_object( "image{$i}.jpg", 0, array(
				'post_mime_type' => 'image/jpeg',
				'post_type'      => 'attachment'
			) );

			wp_update_attachment_metadata( $attachment_id, array( 'width' => 100, 'height' => 100 ) );
			$attachment_ids[] = $attachment_id;
		}

		$attachment_ids = join( ',', $attachment_ids );
		$post_id        = $this->factory->post->create( array( 'post_content' => "[gallery ids='{$attachment_ids}']" ) );


		// Extract the gallery images.
		$media = self::$post_media_extractor->extract( '', BP_Media_Extractor::IMAGES, array(
			'post' => get_post( $post_id ),
		) );

		$this->assertArrayHasKey( 'images', $media );
		$media = array_values( wp_list_filter( $media['images'], array( 'source' => 'galleries' ) ) );
		$this->assertCount( 3, $media );

		for ( $i = 1; $i <= 3; $i++ ) {
			$this->assertSame( 'http://' . WP_TESTS_DOMAIN . "/wp-content/uploads/image{$i}.jpg", $media[ $i - 1 ]['url'] );
		}
	}

	public function test_extract_no_images_from_content_with_invalid_galleries_variant_no_ids() {
		$post_id = $this->factory->post->create( array( 'post_content' => self::$richtext ) );
		$media   = self::$post_media_extractor->extract( self::$richtext, BP_Media_Extractor::IMAGES, array(
			'post' => get_post( $post_id ),
		) );

		$this->assertArrayHasKey( 'images', $media );
		$media = array_values( wp_list_filter( $media['images'], array( 'source' => 'galleries' ) ) );
		$this->assertCount( 0, $media );
	}

	public function test_extract_no_images_from_content_with_invalid_galleries_variant_ids() {
		$post_id = $this->factory->post->create( array( 'post_content' => '[gallery ids="117,4529"]' ) );
		$media   = self::$post_media_extractor->extract( '', BP_Media_Extractor::IMAGES, array(
			'post' => get_post( $post_id ),
		) );

		$this->assertArrayHasKey( 'images', $media );
		$media = array_values( wp_list_filter( $media['images'], array( 'source' => 'galleries' ) ) );
		$this->assertCount( 0, $media );
	}


	/**
	 * Images extraction (thumbnail).
	 */

	public function test_extract_no_images_from_content_with_featured_image() {
		$post_id      = $this->factory->post->create( array( 'post_content' => self::$richtext ) );
		$thumbnail_id = $this->factory->attachment->create_object( 'image.jpg', $post_id, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment'
		) );
		set_post_thumbnail( $post_id, $thumbnail_id );


		// Extract the gallery images.
		$media = self::$post_media_extractor->extract( '', BP_Media_Extractor::IMAGES, array(
			'post' => get_post( $post_id ),
		) );

		$this->assertArrayHasKey( 'images', $media );
		$media = array_values( wp_list_filter( $media['images'], array( 'source' => 'featured_images' ) ) );
		$this->assertCount( 1, $media );

		$this->assertSame( 'http://' . WP_TESTS_DOMAIN . '/wp-content/uploads/image.jpg', $media[0]['url'] );
	}

	public function test_extract_images_from_content_without_featured_image() {
		$post_id = $this->factory->post->create( array( 'post_content' => self::$richtext ) );
		$media   = self::$post_media_extractor->extract( '', BP_Media_Extractor::IMAGES, array(
			'post' => get_post( $post_id ),
		) );

		$this->assertArrayHasKey( 'images', $media );
		$media = array_values( wp_list_filter( $media['images'], array( 'source' => 'featured_images' ) ) );
		$this->assertCount( 0, $media );
	}


	/**
	 * Audio extraction.
	 */

	public function texxxxxst_extract_audio_from_content() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::AUDIO );

		$this->assertArrayHasKey( 'audio', $media );
		$this->assertSame( 1, $media['audio']['caption']['count'] );
		$this->assertSame( 1, $media['audio']['gallery']['count'] );
	}

	public function txxxxxest_extract_no_shortcodes_from_content_with_unregistered_shortcodes() {
	}
}
