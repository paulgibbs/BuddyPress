<?php
/**
 * @group core
 * @group BP_Email_Recipient
 */
class BP_Email_Recipient_Tests extends BP_UnitTestCase {
	protected function $u1;

	public function setUp() {
		parent::setUp();

		$this->u1 = $this->factory->user->create(
			'user_email'    => 'test@example.com',
			'user_nicename' => 'Unit Test',
		);
	}

	public function test_return_with_address_and_name() {
		$email     = 'test@example.com';
		$name      = 'Unit Test';
		$recipient = new BP_Email_Recipient( $email, $name );

		$this->assertSame( $email, $recipient->get_address() );
		$this->assertSame( $name, $recipient->get_name() );
	}

	public function test_return_with_array() {
		$email     = 'test@example.com';
		$name      = 'Unit Test';
		$recipient = new BP_Email_Recipient( array( $email => $name ) );

		$this->assertSame( $email, $recipient->get_address() );
		$this->assertSame( $name, $recipient->get_name() );
	}

	public function test_return_with_user_id() {
		$recipient = new BP_Email_Recipient( $this->u1 );

		$this->assertSame( 'test@example.com', $recipient->get_address() );
		$this->assertSame( 'Unit Test', $recipient->get_name() );
	}

	public function test_return_with_wp_user_object() {
		$recipient = new BP_Email_Recipient( get_user_by( 'id', $this->u1 ) );

		$this->assertSame( 'test@example.com', $recipient->get_address() );
		$this->assertSame( 'Unit Test', $recipient->get_name() );
	}

	public function test_return_with_address_and_optional_name() {
		$email     = 'test@example.com';
		$recipient = new BP_Email_Recipient( $email );

		$this->assertSame( $email, $recipient->get_address() );
		$this->assertEmpty( $recipient->get_name() );
	}

	public function test_return_with_array_and_optional_name() {
		$email     = 'test@example.com';
		$recipient = new BP_Email_Recipient( array( $email ) );

		$this->assertSame( $email, $recipient->get_address() );
		$this->assertEmpty( $recipient->get_name() );
	}

	public function test_should_return_empty_string_if_user_id_id_invalid() {
		$recipient = new BP_Email_Recipient( time() );

		$this->assertEmpty( $recipient->get_address() );
		$this->assertEmpty( $recipient->get_name() );
	}
}
