<?php
/**
 * @group core
 * @group BP_Email
 */
class BP_Tests_Email extends BP_UnitTestCase {
	public function setUp() {
		parent::setUp();
		remove_filter( 'bp_email_get_headers', 'bp_core_set_default_email_headers', 6, 4 );
		remove_filter( 'bp_email_get_tokens', 'bp_core_set_default_email_tokens', 6, 4 );
	}

	public function tearDown() {
		add_filter( 'bp_email_get_tokens', 'bp_core_set_default_email_tokens', 6, 4 );
		add_filter( 'bp_email_get_headers', 'bp_core_set_default_email_headers', 6, 4 );
		parent::tearDown();
	}

	public function test_valid_from_with_no_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$email->from( $address );

		$from = $email->get( 'from' );
		$this->assertSame( $address, key( $from ) );
		$this->assertEmpty( current( $from ) );
	}

	public function test_valid_from_with_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$name    = 'Uni Est';
		$email->from( $address, $name );

		$from = $email->get( 'from' );
		$this->assertSame( $address, key( $from ) );
		$this->assertSame( $name, current( $from ) );
	}

	public function test_valid_reply_to_with_no_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$email->reply_to( $address );

		$reply_to = $email->get( 'reply_to' );
		$this->assertSame( $address, key( $reply_to ) );
		$this->assertEmpty( current( $reply_to ) );
	}

	public function test_valid_reply_to_with_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$name    = 'Uni Est';
		$email->reply_to( $address, $name );

		$reply_to = $email->get( 'reply_to' );
		$this->assertSame( $address, key( $reply_to ) );
		$this->assertSame( $name, current( $reply_to ) );
	}
	public function test_valid_to_with_no_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$email->to( $address );

		$to = $email->get( 'to' );
		$this->assertSame( $address, key( $to ) );
		$this->assertEmpty( current( $to ) );
	}

	public function test_valid_to_with_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$name    = 'Uni Est';
		$email->to( $address, $name );

		$to = $email->get( 'to' );
		$this->assertSame( $address, key( $to ) );
		$this->assertSame( $name, current( $to ) );
	}

	public function test_valid_to_array() {
		$email = new BP_Email( 'fake_type' );

		$address = array( 'test@example.com' => 'Uni Est', 'test2@example.com' => '' );
		$email->to( $address );

		$this->assertEqualSets( $address, $email->get( 'to' ) );
	}

	public function test_valid_cc_with_no_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$email->cc( $address );

		$cc = $email->get( 'cc' );
		$this->assertSame( $address, key( $cc ) );
		$this->assertEmpty( current( $cc ) );
	}

	public function test_valid_cc_with_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$name    = 'Uni Est';
		$email->cc( $address, $name );

		$cc = $email->get( 'cc' );
		$this->assertSame( $address, key( $cc ) );
		$this->assertSame( $name, current( $cc ) );
	}

	public function test_valid_cc_array() {
		$email = new BP_Email( 'fake_type' );

		$address = array( 'test@example.com' => 'Uni Est', 'test2@example.com' => '' );
		$email->cc( $address );

		$this->assertEqualSets( $address, $email->get( 'cc' ) );
	}

	public function test_valid_bcc_with_no_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$email->bcc( $address );

		$bcc = $email->get( 'bcc' );
		$this->assertSame( $address, key( $bcc ) );
		$this->assertEmpty( current( $bcc ) );
	}

	public function test_valid_bcc_with_name() {
		$email = new BP_Email( 'fake_type' );

		$address = 'test@example.com';
		$name    = 'Uni Est';
		$email->bcc( $address, $name );

		$bcc = $email->get( 'bcc' );
		$this->assertSame( $address, key( $bcc ) );
		$this->assertSame( $name, current( $bcc ) );
	}

	public function test_valid_bcc_array() {
		$email = new BP_Email( 'fake_type' );

		$address = array( 'test@example.com' => 'Uni Est', 'test2@example.com' => '' );
		$email->bcc( $address );

		$this->assertEqualSets( $address, $email->get( 'bcc' ) );
	}

	public function test_valid_subject() {
		$message = 'test';
		$email   = new BP_Email( 'fake_type' );

		$email->subject( $message );
		$this->assertSame( $email->get( 'subject' ), $message );
	}

	public function test_valid_content() {
		$message = 'test';
		$email   = new BP_Email( 'fake_type' );

		$email->content( $message );
		$this->assertSame( $email->get( 'content' ), $message );
	}

	public function test_tokens() {
		$original = array( 'test1' => 'hello', '{{test2}}' => 'world' );

		$email = new BP_Email( 'fake_type' );
		$email->tokens( $original );

		$this->assertSame(
			array( '{{test1}}', '{{test2}}' ),
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
		$this->assertSame( $email->get( 'headers' ), $headers );
	}

	public function test_validation() {
		$email = new BP_Email( 'fake_type' );
		$email->from( 'test1@example.com' )->to( 'test2@example.com' )->subject( 'testing' )->content( 'testing' );

		$this->assertTrue( $email->validate() );
	}


	public function test_invalid_from() {
		$email   = new BP_Email( 'fake_type' );
		$address = 'this-is-not-an-email-address';
		$email->from( $address );
		$this->assertSame( $email->get( 'from' ), array() );
	}

	public function test_invalid_reply_to() {
		$email   = new BP_Email( 'fake_type' );
		$address = 'this-is-not-an-email-address';
		$email->reply_to( $address );
		$this->assertSame( $email->get( 'reply_to' ), array() );
	}

	public function test_invalid_to() {
		$email   = new BP_Email( 'fake_type' );
		$address = 'this-is-not-an-email-address';
		$email->to( $address );
		$this->assertEmpty( $email->get( 'to' ) );
	}

	public function test_invalid_cc() {
		$email   = new BP_Email( 'fake_type' );
		$address = 'this-is-not-an-email-address';
		$email->cc( $address );
		$this->assertEmpty( $email->get( 'cc' ) );
	}

	public function test_invalid_bcc() {
		$email   = new BP_Email( 'fake_type' );
		$address = 'this-is-not-an-email-address';
		$email->bcc( $address );
		$this->assertEmpty( $email->get( 'bcc' ) );
	}

	public function test_invalid_tokens() {
		$email = new BP_Email( 'fake_type' );
		$email->tokens( array( 'te{st}1' => 'hello world' ) );

		$this->assertSame(
			array_keys( $email->get( 'tokens' ) ),
			array( '{{test1}}' )
		);
	}

	public function test_invalid_headers() {
		$email = new BP_Email( 'fake_type' );

		$headers = array( 'custom:header' => 'custom:value' );
		$email->headers( $headers );
		$this->assertNotSame( $email->get( 'headers' ), $headers );
		$this->assertSame( $email->get( 'headers' ), array( 'customheader' => 'customvalue' ) );
	}

	public function test_validation_with_missing_required_data() {
		$email  = new BP_Email( 'fake_type' );
		$email->from( 'test1@example.com' )->to( 'test2@example.com' )->subject( 'testing' );
		$result = $email->validate();

		$this->assertTrue( is_wp_error( $result ) );
		$this->assertSame( $result->get_error_code(), 'missing_parameter' );
	}
}
