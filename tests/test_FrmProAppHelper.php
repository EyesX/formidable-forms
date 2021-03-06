<?php
/**
 * @group app
 */
class WP_Test_FrmProAppHelper extends FrmUnitTest {
	/**
	 * @covers FrmProAppHelper::prepare_dfe_text
	 */
	function test_prepare_dfe_text() {
		$test_values_array = self::_setup_test_values();

		foreach( $test_values_array as $test_values ) {

			// Get the actual returned where val
			$new_where_val = self::_get_dynamic_entry_ids( $test_values['form_key'], $test_values['where_field_key'], array( 'where_val' => $test_values['where_val'], 'where_is' => $test_values['where_is'], 'display' => false ) );

			// Get the expected where val
			$expected_where_val = $test_values['expected_where_val'];

			// Compare expected vs. actual where val
			$this->assertEquals( $expected_where_val, $new_where_val, 'Linked entry IDs are not being retrieved correctly in Dynamic field filters when filter says: ' . $test_values['nickname'] );
		}
	}

	function _get_dynamic_entry_ids( $form_key, $where_field_key, $args ) {
		// Get where_field
		$where_field = FrmField::getOne( $where_field_key );

		// Get all entry IDs for form
		$form_id = $this->factory->form->get_id_by_key( $form_key );
		$entry_ids = FrmEntry::getAll( array( 'it.form_id' => $form_id ), '', '', false, false );

		// Prepare the args
		self::_do_prepare_where_args( $args, $where_field, $entry_ids );

		// Set new where_val
		self::_do_prepare_dfe_text( $args, $where_field );

		return $args['where_val'];
	}

	function _setup_test_values(){
		$utah_entry_id = $this->factory->entry->get_id_by_key( 'utah_entry' );

		$test_values = array();

		// Dynamic field is equal to Utah
		$test_values[] = array(
			'nickname'	=> 'dynamic field is equal to val',
			'where_field_key'	=> 'dynamic-state-level-1',
			'where_val' => 'Utah',
			'where_is'	=> '=',
			'form_key'	=> 'city_form',
			'expected_where_val'	=> array( $utah_entry_id )
		);

		// Dynamic field is NOT equal to Utah
		// NOTE: Remember opposite should be returned for NOT filters
		$test_values[] = array(
			'nickname'	=> 'dynamic field is NOT equal to val',
			'where_field_key'	=> 'dynamic-state-level-1',
			'where_val' => 'Utah',
			'where_is'	=> '!=',
			'form_key'	=> 'city_form',
			'expected_where_val'	=> array( $utah_entry_id )
		);

		// Dynamic field is like Ut
		$test_values[] = array(
			'nickname'	=> 'dynamic field is LIKE val',
			'where_field_key'	=> 'dynamic-state-level-1',
			'where_val' => 'Utah',
			'where_is'	=> 'LIKE',
			'form_key'	=> 'city_form',
			'expected_where_val'	=> array( $utah_entry_id )
		);

		// Dynamic field is NOT like Ut
		// NOTE: Remember opposite should be returned for NOT filters
		$test_values[] = array(
			'nickname'	=> 'dynamic field is not LIKE val',
			'where_field_key'	=> 'dynamic-state-level-1',
			'where_val' => 'Utah',
			'where_is'	=> 'not LIKE',
			'form_key'	=> 'city_form',
			'expected_where_val'	=> array( $utah_entry_id )
		);

		// Dynamic field is equal to ____(blank)
		$test_values[] = array(
			'nickname'	=> 'dynamic field is equal to blank',
			'where_field_key'	=> 'dynamic-state-level-1',
			'where_val' => '',
			'where_is'	=> '=',
			'form_key'	=> 'city_form',
			'expected_where_val'	=> ''
		);

		// Dynamic field is not equal to ______(blank)
		$test_values[] = array(
			'nickname'	=> 'dynamic field is NOT equal to blank',
			'where_field_key'	=> 'dynamic-state-level-1',
			'where_val' => '',
			'where_is'	=> '!=',
			'form_key'	=> 'city_form',
			'expected_where_val'	=> ''
		);

		// Dynamic field is equal to entry key
		$test_values[] = array(
			'nickname'	=> 'dynamic field is equal to entry key',
			'where_field_key'	=> 'dynamic-state-level-1',
			'where_val' => 'utah_entry',
			'where_is'	=> '=',
			'form_key'	=> 'city_form',
			'expected_where_val'	=> array( $utah_entry_id )
		);

		// Dynamic field is equal to entry ID
		$test_values[] = array(
			'nickname'	=> 'dynamic field is equal to entry ID',
			'where_field_key'	=> 'dynamic-state-level-1',
			'where_val' => $utah_entry_id,
			'where_is'	=> '=',
			'form_key'	=> 'city_form',
			'expected_where_val'	=> $utah_entry_id
		);

		// Dynamic field is equal to array
		$entry_id = $this->factory->entry->get_id_by_key( 'utah_entry' );
		$test_values[] = array(
			'nickname'	=> 'dynamic field is equal to array',
			'where_field_key'	=> 'dynamic-state-level-1',
			'where_val' => array(1,2,3),
			'where_is'	=> '=',
			'form_key'	=> 'city_form',
			'expected_where_val'	=> array(1,2,3)
		);

		return $test_values;
	}

	function _do_prepare_where_args( &$args, $where_field, $entry_ids=array() ){
		$class = new ReflectionClass('FrmProAppHelper');
		$method = $class->getMethod( 'prepare_where_args' );
		$method->setAccessible(true);
		$method->invokeArgs( null, array( &$args, $where_field, $entry_ids ) );
	}

	function _do_prepare_dfe_text( &$args, $where_field ){
		$class = new ReflectionClass('FrmProAppHelper');
		$method = $class->getMethod('prepare_dfe_text');
		$method->setAccessible(true);
		$method->invokeArgs( null, array( &$args, $where_field ) );
	}
}