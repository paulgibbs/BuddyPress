<?php
/**
 * @group core
 * @group BP_Media_Extractor
 */
class BP_Tests_BP_User_Query_TestCases extends BP_UnitTestCase {
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
