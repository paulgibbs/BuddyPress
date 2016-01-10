<?php
/**
 * @group core
 * @group BP_Email
 */
class BP_Tests_Email extends BP_UnitTestCase {
	public function setUp() {
		parent::setUp();
		remove_filter( 'bp_email_get_headers', 'bp_email_set_default_headers', 6, 4 );
		remove_filter( 'bp_email_get_tokens', 'bp_email_set_default_tokens', 6, 4 );
	}

	public function tearDown() {
		add_filter( 'bp_email_get_tokens', 'bp_email_set_default_tokens', 6, 4 );
		add_filter( 'bp_email_get_headers', 'bp_email_set_default_headers', 6, 4 );
		parent::tearDown();
	}

	public function test_valid_subject() {
		$message = 'test';
		$email   = new BP_Email( 'fake_type' );

		$email->subject( $message );
		$this->assertSame( $message, $email->get( 'subject' ) );
	}

	public function test_valid_html_content() {
		$message = '<b>test</b>';
		$email   = new BP_Email( 'fake_type' );

		$email->content_html( $message );
		$email->content_type( 'html' );

		$this->assertSame( $message, $email->get( 'content' ) );
	}

	public function test_valid_plaintext_content() {
		$message = 'test';
		$email   = new BP_Email( 'fake_type' );

		$email->content_plaintext( $message );
		$email->content_type( 'plaintext' );

		$this->assertSame( $message, $email->get( 'content' ) );
	}

	public function test_valid_template() {
		$message = 'test';
		$email   = new BP_Email( 'fake_type' );

		$email->template( $message );
		$this->assertSame( $message, $email->get( 'template' ) );
	}

	public function test_tokens() {
		$original = array( 'test1' => 'hello', 'test2' => 'world' );

		$email = new BP_Email( 'fake_type' );
		$email->tokens( $original );

		$this->assertSame(
			array( 'test1', 'test2' ),
			array_keys( $email->get( 'tokens' ) )
		);

		$this->assertSame(
			array( 'hello', 'world' ),
			array_values( $email->get( 'tokens' ) )
		);
	}

	public function test_headers() {
		$email = new BP_Email( 'fake_type' );

		$headers = array( 'custom_header' => 'custom_value' );
		$email->headers( $headers );
		$this->assertSame( $headers, $email->get( 'headers' ) );
	}

	public function test_validation() {
		$email = new BP_Email( 'fake_type' );
		$email->from( 'test1@example.com' )->to( 'test2@example.com' )->subject( 'testing' );
		$email->content_html( 'testing' );

		$this->assertTrue( $email->validate() );
	}

	public function test_invalid_characters_are_stripped_from_tokens() {
		$email = new BP_Email( 'fake_type' );
		$email->tokens( array( 'te{st}1' => 'hello world' ) );

		$this->assertSame(
			array( 'test1' ),
			array_keys( $email->get( 'tokens' ) )
		);
	}

	public function test_token_are_escaped() {
		$token = '<blink>';
		$email = new BP_Email( 'fake_type' );
		$email->content_html( '{{test}}' )->tokens( array( 'test' => $token ) );

		$this->assertSame(
			esc_html( $token ),
			$email->get( 'content', 'replace-tokens' )
		);
	}

	public function test_token_are_not_escaped() {
		$token = '<blink>';
		$email = new BP_Email( 'fake_type' );
		$email->content_html( '{{{test}}}' )->tokens( array( 'test' => $token ) );

		$this->assertSame(
			$token,
			$email->get( 'content', 'replace-tokens' )
		);
	}

	public function test_invalid_headers() {
		$email = new BP_Email( 'fake_type' );

		$headers = array( 'custom:header' => 'custom:value' );
		$email->headers( $headers );
		$this->assertNotSame( $headers, $email->get( 'headers' ) );
		$this->assertSame( array( 'customheader' => 'customvalue' ), $email->get( 'headers' ) );
	}

	public function test_validation_with_missing_required_data() {
		$email  = new BP_Email( 'fake_type' );
		$email->from( 'test1@example.com' )->to( 'test2@example.com' )->subject( 'testing' );  // Content
		$result = $email->validate();

		$this->assertTrue( is_wp_error( $result ) );
		$this->assertSame( 'missing_parameter', $result->get_error_code() );
	}

	public function test_validation_with_missing_template() {
		$email  = new BP_Email( 'fake_type' );
		$email->from( 'test1@example.com' )->to( 'test2@example.com' )->subject( 'testing' );
		$email->content_html( 'testing' )->template( '' );
		$result = $email->validate();

		// Template has a default value, but it can't be blank.
		$this->assertTrue( is_wp_error( $result ) );
		$this->assertSame( $result->get_error_code(), 'missing_parameter' );
	}
}
