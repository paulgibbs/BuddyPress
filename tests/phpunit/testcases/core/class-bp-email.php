<?php
/**
 * @group core
 * @group BP_Email
 */
class BP_Tests_Email extends BP_UnitTestCase {
	public function test_valid_from_with_no_name() {
		$email = new BP_Email();

		$address = 'test@example.com';
		$email->from( $address );
		$this->assertSame( $email->get( 'from' ), $address );
		$this->assertEmpty( $email->get( 'from_name' ) );
	}

	public function test_valid_from_name() {
		$email = new BP_Email();

		$name = 'Uni Est';
		$email->from_name( $name );
		$this->assertSame( $email->get( 'from_name' ), $name );
	}

	public function test_valid_to_with_no_name() {
		$email = new BP_Email();

		$address = 'test@example.com';
		$email->to( $address );
		$results = $email->get( 'to' );
		$this->assertArrayHasKey( $address, $results );
		$this->assertEmpty( $results[ $address ] );

		$address = '<test@example.com>';
		$email->to( $address );
		$results = $email->get( 'to' );
		$this->assertArrayHasKey( $address, $results );
		$this->assertEmpty( $results[ $address ] );
	}

	public function test_valid_to_with_name() {
		$email   = new BP_Email();
		$address = 'test@example.com';
		$name    = 'some person';

		$email->to( "{$name} <{$address}>" );
		$results = $email->get( 'to' );
		$this->assertArrayHasKey( $address, $results );
		$this->assertSame( $results[ $address ], $name );
	}

	public function test_valid_to_array() {
		$email = new BP_Email();

		$address = array( 'test@example.com', 'test2@example.com' );
		$results = $email->get( 'to' );

		$this->assertArrayHasKey( $address[0], $results );
		$this->assertEmpty( $results[ $address ] );
	}

	public function test_valid_cc_with_no_name() {
		$email = new BP_Email();

		$address = 'test@example.com';
		$email->cc( $address );
		$results = $email->get( 'cc' );
		$this->assertArrayHasKey( $address, $results );
		$this->assertEmpty( $results[ $address ] );

		$address = '<test@example.com>';
		$email->cc( $address );
		$results = $email->get( 'cc' );
		$this->assertArrayHasKey( $address, $results );
		$this->assertEmpty( $results[ $address ] );
	}

	public function test_valid_cc_with_name() {
		$email   = new BP_Email();
		$address = 'test@example.com';
		$name    = 'some person';

		$email->cc( "{$name} <{$address}>" );
		$results = $email->get( 'cc' );
		$this->assertArrayHasKey( $address, $results );
		$this->assertSame( $results[ $address ], $name );
	}

	public function test_valid_cc_array() {
		$address = array( 'test@example.com', 'test2@example.com' );
		$email   = new BP_Email();

		$email->cc( $address );
		$this->assertSame( $email->get( 'cc' ), $address );
	}

	public function test_valid_bcc_with_no_name() {
		$email = new BP_Email();

		$address = 'test@example.com';
		$email->bcc( $address );
		$results = $email->get( 'bcc' );
		$this->assertArrayHasKey( $address, $results );
		$this->assertEmpty( $results[ $address ] );

		$address = '<test@example.com>';
		$email->bcc( $address );
		$results = $email->get( 'bcc' );
		$this->assertArrayHasKey( $address, $results );
		$this->assertEmpty( $results[ $address ] );
	}

	public function test_valid_bcc_with_name() {
		$email   = new BP_Email();
		$address = 'test@example.com';
		$name    = 'some person';

		$email->bcc( "{$name} <{$address}>" );
		$results = $email->get( 'bcc' );
		$this->assertArrayHasKey( $address, $results );
		$this->assertSame( $results[ $address ], $name );
	}

	public function test_valid_bcc_array() {
		$address = array( 'test@example.com', 'test2@example.com' );
		$email   = new BP_Email();

		$email->bcc( $address );
		$this->assertSame( $email->get( 'bcc' ), $address );
	}

	public function test_valid_subject() {
		$message = 'test';
		$email   = new BP_Email();

		$email->subject( $message );
		$this->assertSame( $email->get( 'subject' ), $message );
	}

	public function test_valid_body() {
		$message = 'test';
		$email   = new BP_Email();

		$email->body( $message );
		$this->assertSame( $email->get( 'body' ), $message );
	}

	public function test_tokens() {
		$email = new BP_Email();
		$email->tokens( array( 'test1', '{{test2}}' ) );

		$this->assertSame(
			$email->get( 'tokens' ),
			array( '{{test1}}', '{{test2}}' )
		);
	}

	public function test_headers() {
		$email = new BP_Email();

		$headers = array( 'custom_header' => 'custom_value' );
		$email->headers( $headers );
		$this->assertSame( $email->get( 'headers' ), $headers );
	}

	public function test_validation() {
		$email = new BP_Email();
		$email->from( 'test1@example.com' )->to( 'test2@example.com' )->subject( 'testing' )->body( 'testing' );

		$this->assertTrue( $email->validate() );
	}


	public function test_invalid_from() {
		$address = 'test@example.com <Test Example>';
		$email   = new BP_Email();
		$email->from( $address );
		$this->assertSame( $email->get( 'from' ), '' );

		$address = 'this-is-not-an-email-address';
		$email->from( $address );
		$this->assertSame( $email->get( 'from' ), '' );
	}

	public function test_invalid_to() {
		$email   = new BP_Email();
		$address = 'this-is-not-an-email-address';
		$email->to( $address );
		$this->assertEmpty( $email->get( 'to' ) );
	}

	public function test_invalid_cc() {
		$email   = new BP_Email();
		$address = 'this-is-not-an-email-address';
		$email->cc( $address );
		$this->assertEmpty( $email->get( 'cc' ) );
	}

	public function test_invalid_bcc() {
		$email   = new BP_Email();
		$address = 'this-is-not-an-email-address';
		$email->bcc( $address );
		$this->assertEmpty( $email->get( 'bcc' ) );
	}

	public function test_invalid_tokens() {
		$email = new BP_Email();
		$email->tokens( array( 'te{st}1' ) );

		$this->assertSame( $email->get( 'tokens' ), array( '{{test1}}' ) );
	}

	public function test_invalid_headers() {
		$email = new BP_Email();

		$headers = array( 'custom:header' => 'custom:value' );
		$email->headers( $headers );
		$this->assertNotSame( $email->get( 'headers' ), $headers );
		$this->assertSame( $email->get( 'headers' ), array( 'customheader' => 'customvalue' ) );
	}

	public function test_validation_with_missing_required_data() {
		$email  = new BP_Email();
		$email->from( 'test1@example.com' )->to( 'test2@example.com' )->subject( 'testing' );
		$result = $email->validate();

		$this->assertTrue( is_wp_error( $result ) );
		$this->assertSame( $result->get_error_code(), 'missing_parameter' );
	}
}
