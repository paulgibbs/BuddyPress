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
		<a href='http://example.com/'><img src='http://example.com/image-in-a-link.gif' /></a></strong>.
		It definitely does not like <img src='data:1234567890A'>data URIs</img>. @

		The parser only extracts wp_allowed_protocols() protocols, not something like <a href='phone:004400'>phone</a>.

		[caption]Here is a caption shortcode.[/caption]

		There are two types of [gallery] shortcodes; one like that, and another with [gallery ids='100, 101, 102'].
		";
	}

	public function setUp() {
		parent::setUp();

		$this->factory->user->create( array( 'user_login' => 'paulgibbs' ) );
	}

	public function tearDown() {
		parent::tearDown();
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

		foreach ( $media['shortcodes'] as $shortcode_type => $shortcode ) {
			foreach ( $shortcode as $item ) {
				$this->assertArrayHasKey( 'count', $item );
				$this->assertInternalType( 'int', $item['count'] );
			}
		}

		foreach ( $media['embeds'] as $item ) {
			$this->assertArrayHasKey( 'url', $item );
			$this->assertInternalType( 'string', $item['url'] );
			$this->assertNotEmpty( $item['url'] );
		}
	}

	// "has" counts, etc
	public function test_check_media_extraction_counts_are_correct() {
		$media = self::$media_extractor->extract( self::$richtext );

		foreach ( $media['has'] as $type => $total ) {
			$this->assertTrue( count( $media[ $type ] ) === $total, "Difference with the 'has' count for {$type}." );
		}
	}


	public function test_extract_multiple_media_types_from_content() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::LINKS | BP_Media_Extractor::MENTIONS );

		$this->assertArrayHasKey( 'has', $media );
		$this->assertArrayHasKey( 'links', $media );
		$this->assertArrayHasKey( 'mentions', $media );
		$this->assertArrayNotHasKey( 'shortcodes', $media );
	}


	/**
	 * Link extraction.
	 */

	// both quote styles
	public function test_extract_links_from_content() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::LINKS );

		$this->assertCount( 2, $media );
		$this->assertArrayHasKey( 'has', $media );
		$this->assertArrayHasKey( 'links', $media );

		$this->assertArrayHasKey( 'links', $media['has'] );
	}

	// non-https? links, empty links, data: URIs
	public function test_extract_no_links_from_content_with_invalid_links() {}


	/**
	 * at-mentions extraction.
	 */

	public function test_extract_mentions_from_content_with_activity_enabled() {}

	public function test_extract_mentions_from_content_with_activity_disabled() {}


	/**
	 * Shortcodes extraction.
	 */

	public function test_extract_shortcodes_from_content() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::SHORTCODES );

		$this->assertCount( 2, $media );
		$this->assertArrayHasKey( 'has', $media );
		$this->assertArrayHasKey( 'shortcodes', $media );

		$this->assertArrayHasKey( 'shortcodes', $media['has'] );
	}

	public function test_extract_no_shortcodes_from_content_with_unregistered_shortcodes() {}


	/**
	 * oEmbeds extraction.
	 */

	public function test_extract_oembeds_from_content() {
		$media = self::$media_extractor->extract( self::$richtext, BP_Media_Extractor::EMBEDS );

		$this->assertCount( 2, $media );
		$this->assertArrayHasKey( 'has', $media );
		$this->assertArrayHasKey( 'embeds', $media );

		$this->assertArrayHasKey( 'embeds', $media['has'] );
	}


	/**
	 * Images extraction (src tags).
	 */

	// both quote styles
	public function test_extract_images_from_content_with_src_tags() {}

	// empty src attributes, data: URIs
	public function test_extract_no_images_from_content_with_invalid_src_tags() {}


	/**
	 * Images extraction (galleries).
	 */

	// IDs and non-IDs
	public function test_extract_images_from_content_with_galleries() {}

	// IDs only, and non-ID without attachments
	public function test_extract_no_images_from_content_with_invalid_galleries() {}


	/**
	 * Images extraction (thumbnail).
	 */

	public function test_extract_images_from_content_with_thumbnail() {}

	public function test_extract_no_images_from_content_without_thumbnail() {}
}
