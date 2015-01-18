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
		It definitely does not like <img src='data:1234567890A'>data URIs</img>.

		The parser only extracts wp_allowed_protocols() protocols, not something like <a href='phone:004400'>phone</a>.

		[caption]Here is a caption shortcode.[/caption]

		There are two types of [gallery] shortcodes; one like that, and another with [gallery ids='100, 101, 102'].
		";
	}

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}


	/**
	 * General.
	 */

	// check types/ints, strings, etc
	public function test_check_media_extraction_return_types() {}

	// "has" counts, etc
	public function test_check_media_extraction_return_counts() {}


	public function test_extract_multiple_media_types_from_content() {}


	/**
	 * Link extraction.
	 */

	// both quote styles
	public function test_extract_links_from_content() {}

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

	public function test_extract_shortcodes_from_content() {}

	public function test_extract_no_shortcodes_from_content_with_unregistered_shortcodes() {}


	/**
	 * oEmbeds extraction.
	 */

	// wildcard and non-wildcard versions
	public function test_extract_oembeds_from_content() {}


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
