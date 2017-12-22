<?php

/**
 * Class LA_Events_ACF
 *
 * @since 1.0.0
 * @since 1.0.1 Added fields for time, and all day events
 * @since 1.0.5 Added end date
 */
class LA_Events_ACF {

	const EVENT_START_DATE_FIELD = 'la_event_start_date';
	const EVENT_START_DATE_TIME_FIELD = 'la_event_start_date_time';
	const EVENT_END_DATE_FIELD = 'la_event_end_date';
	const EVENT_END_DATE_TIME_FIELD = 'la_event_end_date_time';
	const EVENT_ALL_DAY_FIELD = 'la_event_allday';

	/**
	 * Initialization
	 */
	public static function init() {

		self::loadLocalJSONFields();

	}

	/**
	 * Load ACF Local JSON Fields
	 *
	 * @since 1.0.1 Added fields for all-day events
	 * @since 1.0.5 Added fields for end-date
	 */
	public static function loadLocalJSONFields() {

		if (function_exists('acf_add_local_field_group')):

			acf_add_local_field_group(array(
				'key' => 'group_5a33f6a7f0069',
				'title' => 'Events',
				'fields' => array(
					array(
						'key' => 'field_5a37d35490081',
						'label' => 'All day',
						'name' => 'la_event_allday',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 1,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_5a33f6b0d31b7',
						'label' => 'Event start date and time',
						'name' => 'la_event_start_date_time',
						'type' => 'date_time_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5a37d35490081',
									'operator' => '!=',
									'value' => '1',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'display_format' => 'd/m/Y g:i a',
						'return_format' => 'c',
						'first_day' => 1,
					),
					array(
						'key' => 'field_5a3cb7e996070',
						'label' => 'Event end date and time',
						'name' => 'la_event_end_date_time',
						'type' => 'date_time_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5a37d35490081',
									'operator' => '!=',
									'value' => '1',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'display_format' => 'd/m/Y g:i a',
						'return_format' => 'c',
						'first_day' => 1,
					),
					array(
						'key' => 'field_5a37d36d90083',
						'label' => 'Event start date',
						'name' => 'la_event_start_date',
						'type' => 'date_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5a37d35490081',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'display_format' => 'd/m/Y',
						'return_format' => 'c',
						'first_day' => 1,
					),
					array(
						'key' => 'field_5a3cb7f596071',
						'label' => 'Event end date',
						'name' => 'la_event_end_date',
						'type' => 'date_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5a37d35490081',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'display_format' => 'd/m/Y',
						'return_format' => 'c',
						'first_day' => 1,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'la-event',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => 1,
				'description' => '',
			));

		endif;
	}

}