<?php

/**
 * Class LA_Events_Helper
 *
 * @since 1.0.0
 */
class LA_Events_Helper {

	/**
	 * Build query for events WP Query
	 *
	 * @param array $metaQuery WP_Query meta_query array
	 * @param array $taxQuery WP_Query tax_query array
	 *
	 * @return array Array of WP_Post objects
	 */
	public static function buildEventsQuery($metaQuery = array(), $taxQuery = array()) {

		$queryArgs = array(
			'post_type' => LA_Events_Core::EVENT_POST_TYPE
		);

		if (!empty($metaQuery)) {
			$queryArgs['meta_query'] = $metaQuery;
		}

		if (!empty($taxQuery)) {
			$queryArgs['tax_query'] = $taxQuery;
		}

		$queryPosts = new WP_Query($queryArgs);

		return $queryPosts->posts;

	}

	/**
	 * Build events objects
	 *
	 * @param array $posts Array with WP_Post objects
	 *
	 * @return array|bool Array of events objects, or false if initial array was empty
	 */
	public static function buildEventsObject($posts = array()) {

		if (empty($posts)) {
			return FALSE;
		}

		$eventsObject = array();

		foreach ($posts as $post) {

			$eventId = $post->ID;
			$eventDate = get_field(LA_Events_ACF::EVENT_DATE_FIELD, $eventId);
			$eventStringDate = self::getStrToTimeEventDate($eventId);

			array_push($eventsObject, array(
				'ID' => $eventId,
				'title' => $post->post_title,
				'content' => $post->post_content,
				'event_date' => $eventDate,
				'event_date_timestamp' => $eventStringDate
			));
		}

		return $eventsObject;

	}

	/**
	 * Convert date to timestamp
	 *
	 * @param int $eventId Event ID
	 *
	 * @return bool|int Timestamp for event date, or false if event ID is zero
	 */
	public static function getStrToTimeEventDate($eventId = 0) {

		$eventId = intval($eventId);

		if ($eventId === 0) {
			return FALSE;
		}

		$eventDate = get_field(LA_Events_ACF::EVENT_DATE_FIELD, $eventId);

		$stringDate = strtotime($eventDate);

		return $stringDate;

	}

	/**
	 * Translate events object into calendar object
	 *
	 * @param array $eventsObject Array with events
	 *
	 * @return array|bool Array with events formatted into calendar request, or false if events object is empty
	 */
	public static function convertEventsObjectIntoCalendarObject($eventsObject = array()) {

		if (empty($eventsObject)) {
			return FALSE;
		}

		$calendarObject = array();

		foreach ($eventsObject as $eventObject) {

			array_push($calendarObject, array(
				'title' => $eventObject['title'],
				'start' => $eventObject['event_date']
			));

		}

		return $calendarObject;

	}

	/**
	 * Get all event categories
	 *
	 * @return array Array with categories
	 */
	public static function getEventsCategories() {

		$categories = get_terms(array(
			'taxonomy' => LA_Events_Core::EVENT_POST_TYPE_CATEGORY,
			'hide_empty' => FALSE
		));

		$categoriesObject = array();

		if (!empty($categories)) {
			foreach ($categories as $category) {
				array_push($categoriesObject, array(
					'ID' => $category->term_id,
					'name' => $category->name,
					'slug' => $category->slug
				));
			}
		}

		return $categoriesObject;
	}

}