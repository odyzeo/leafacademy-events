<?php

/**
 * Class LA_Events_REST
 *
 * @sine 1.0.0
 */
class LA_Events_REST {

	/**
	 * Initialization
	 */
	public static function init() {

		add_action('rest_api_init', array(__CLASS__, 'registerRestApiEndpoints'));

	}

	/**
	 * Register own REST API endpoints
	 */
	public static function registerRestApiEndpoints() {

		register_rest_route('la-events-calendar/v1', '/event/intStart=(?P<intStart>\d+)&intEnd=(?P<intEnd>\d+)&category=(?P<category>\d+)', array(
			'methods' => 'GET',
			'callback' => array(__CLASS__, 'getEventsEndpoint')
		));

	}

	/**
	 * Get events from REST API
	 *
	 * @param $data array Array with data from request
	 *
	 * @return array REST API response
	 */
	public static function getEventsEndpoint($data) {

		$startInterval = $data['intStart'];
		$endInterval = $data['intEnd'];
		$category = $data['category'];

		$startIntervalDate = date('Y-m-d', $startInterval);
		$endIntervalDate = date('Y-m-d', $endInterval);

		$category = intval($category);

		$taxQuery = array();
		if ($category !== 0) {
			$taxQuery = array(
				array(
					'taxonomy' => LA_Events_Core::EVENT_POST_TYPE_CATEGORY,
					'field' => 'term_id',
					'terms' => $category
				)
			);
		}

		$metaQuery = array(
			array(
				'key' => LA_Events_ACF::EVENT_DATE_FIELD,
				'value' => array($startIntervalDate, $endIntervalDate),
				'compare' => 'BETWEEN',
				'type' => 'DATE'
			)
		);

		$events = LA_Events_Helper::buildEventsQuery($metaQuery, $taxQuery);
		$eventsObject = LA_Events_Helper::buildEventsObject($events);
		$calendarObject = LA_Events_Helper::convertEventsObjectIntoCalendarObject($eventsObject);

		return array(
			'events' => $calendarObject
		);

	}

}