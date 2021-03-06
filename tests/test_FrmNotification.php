<?php

/**
 * @group notification
 */
class WP_Test_FrmNotification extends FrmUnitTest {

	public function test_class_is_tested() {
		$this->assertTrue( true );
	}

	/**
	 * @covers FrmNotification::trigger_email
	 */
	public function test_trigger_email(){
		// get the imported form with the email action
		$form = $this->factory->form->get_object_by_id( $this->contact_form_key );

		// get the email settings
		$actions = FrmFormAction::get_action_for_form( $form->id, 'email' );
		$this->assertNotEmpty( $actions );

		$entry_data = $this->factory->field->generate_entry_array( $form );
		$entry_id = $this->factory->entry->create( $entry_data );

		$entry = FrmEntry::getOne( $entry_id, true );
		$this->assertNotEmpty( $entry );

		foreach ( $actions as $action ) {
			$expected_to = $action->post_content['email_to'];
			FrmNotification::trigger_email( $action, $entry, $form );

			$this->assertEquals( $expected_to, $GLOBALS['phpmailer']->mock_sent[0]['to'][0][0] );
			//$this->assertNotEmpty( strpos( $GLOBALS['phpmailer']->mock_sent[0]['header'], 'Reply-To: admin@example.org' ) );

			// TODO: check email body, reply to, cc, bcc, from
		}
	}
}