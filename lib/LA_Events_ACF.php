<?php

/**
 * Class LA_Events_ACF
 *
 * @since 1.0.0
 */
class LA_Events_ACF {

	const EVENT_DATE_FIELD = 'la_event_date';

	/**
	 * Initialization
	 */
	public static function init() {

		self::loadLocalJSONFields();

	}

	/**
	 * Load ACF Local JSON Fields
	 */
	public static function loadLocalJSONFields() {

		if( function_exists('acf_add_local_field_group') ):

			acf_add_local_field_group(array(
				'key' => 'group_5a33f6a7f0069',
				'title' => 'Events',
				'fields' => array(
					array(
						'key' => 'field_5a33f6b0d31b7',
						'label' => 'Event date',
						'name' => 'la_event_date',
						'type' => 'date_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'display_format' => 'd/m/Y',
						'return_format' => 'Y-m-d',
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