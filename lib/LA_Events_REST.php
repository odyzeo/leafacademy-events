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

		register_rest_route('la-events-calendar/v1', '/event-mobile/page=(?P<page>\d+)&per_page=(?P<per_page>\d+)&category=(?P<category>\d+)', array(
			'methods' => 'GET',
			'callback' => array(__CLASS__, 'getEventsMobileEndpoint')
		));

	}

	/**
	 * Get events for mobile from REST API
	 *
	 * @param $data array Array with data from request
	 *
	 * @return array REST API response
	 *
	 * @since 1.0.6
	 */
	public static function getEventsMobileEndpoint($data) {

		$page = $data['page'] + 1;
		$perPage = $data['per_page'];
		$category = intval($data['category']);

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

		$today = current_time('Ymd');

		$metaQuery = array(

			'relation' => 'OR',
			array(
				'relation' => 'OR',
				array(
					'key' => LA_Events_ACF::EVENT_START_DATE_FIELD,
					'value' => $today,
					'compare' => '>=',
				),
				array(
					'key' => LA_Events_ACF::EVENT_END_DATE_FIELD,
					'value' => $today,
					'compare' => '<',
				)
			),
			array(
				'relation' => 'OR',
				'datetime' => array(
					'key' => LA_Events_ACF::EVENT_START_DATE_TIME_FIELD,
					'value' => $today,
					'compare' => '>=',
				),
				'date' => array(
					'key' => LA_Events_ACF::EVENT_END_DATE_TIME_FIELD,
					'value' => $today,
					'compare' => '<',
				)
			)
		);

		$events = LA_Events_Helper::getEventsFromWPQuery($metaQuery, $taxQuery, TRUE, $page, $perPage);
		$eventsCounted = (intval(LA_Events_Helper::getEventsFromWPQuery($metaQuery, $taxQuery, TRUE, $page, $perPage, TRUE)) / LA_Events_Core::DEFAULT_EVENT_PER_PAGE);
		$eventsObject = LA_Events_Helper::buildEventsObject($events);

		return array(
			'items' => $eventsObject,
			'total' => $eventsCounted,
			'per_page' => $perPage,
			'page' => $page
		);

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

			'relation' => 'OR',
			array(
				'relation' => 'AND',
				array(
					'key' => LA_Events_ACF::EVENT_START_DATE_FIELD,
					'value' => array($startIntervalDate, $endIntervalDate),
					'compare' => 'BETWEEN',
					'type' => 'DATE'
				),
				array(
					'key' => LA_Events_ACF::EVENT_END_DATE_FIELD,
					'value' => array($startIntervalDate, $endIntervalDate),
					'compare' => 'BETWEEN',
					'type' => 'DATE'
				)
			),
			array(
				'relation' => 'AND',
				array(
					'key' => LA_Events_ACF::EVENT_START_DATE_TIME_FIELD,
					'value' => array($startIntervalDate, $endIntervalDate),
					'compare' => 'BETWEEN',
					'type' => 'DATE'
				),
				array(
					'key' => LA_Events_ACF::EVENT_END_DATE_TIME_FIELD,
					'value' => array($startIntervalDate, $endIntervalDate),
					'compare' => 'BETWEEN',
					'type' => 'DATE'
				)
			)
		);

		$events = LA_Events_Helper::getEventsFromWPQuery($metaQuery, $taxQuery);
		$eventsObject = LA_Events_Helper::buildEventsObject($events);
		$calendarObject = LA_Events_Helper::convertEventsObjectIntoCalendarObject($eventsObject);

		return array(
			'events' => $calendarObject
		);

	}

}