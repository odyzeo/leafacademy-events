<?php

/**
 * Class LA_Events_Helper
 *
 * @since 1.0.0
 */
class LA_Events_Helper {

	/**
	 * Build posts from WP_Query
	 *
	 * @param array $metaQuery WP_Query meta_query array
	 * @param array $taxQuery WP_Query tax_query array
	 *
	 * @return array Array of WP_Post objects
	 */
	public static function getEventsFromWPQuery($metaQuery = array(), $taxQuery = array()) {

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
	 *
	 * @since 1.0.1 Parameters for all-day events added
	 * @since 1.0.5 Added event end parameters into object
	 *              Added event category parameters
	 */
	public static function buildEventsObject($posts = array()) {

		if (empty($posts)) {
			return FALSE;
		}

		$eventsObject = array();

		foreach ($posts as $post) {

			$eventId = $post->ID;
			$eventDate = get_field(LA_Events_ACF::EVENT_START_DATE_TIME_FIELD, $eventId);
			$eventEndDate = get_field(LA_Events_ACF::EVENT_END_DATE_TIME_FIELD, $eventId);
			$eventAllDay = get_field(LA_Events_ACF::EVENT_ALL_DAY_FIELD, $eventId);

			$eventCategory = wp_get_post_terms($eventId, LA_Events_Core::EVENT_POST_TYPE_CATEGORY)[0];
			$eventCategoryColor = LA_Events_Core::DEFAULT_EVENT_CATEGORY_COLOR;
			$eventCategoryId = $eventCategory->term_id;

			if ($eventAllDay) {
				$eventDate = get_field(LA_Events_ACF::EVENT_START_DATE_FIELD, $eventId);
				$eventEndDate = get_field(LA_Events_ACF::EVENT_END_DATE_FIELD, $eventId);
			}

			$categoryTermColor = get_term_meta($eventCategoryId, 'color', TRUE);

			if (!empty($categoryTermColor)) {
				$eventCategoryColor = get_term_meta($eventCategoryId, 'color', TRUE);
			}

			array_push($eventsObject, array(
				'ID' => $eventId,
				'title' => $post->post_title,
				'content' => $post->post_content,
				'event_date' => $eventDate,
				'event_end_date' => $eventEndDate,
				'all_day' => $eventAllDay,
				'category' => array(
					'id' => $eventCategoryId,
					'name' => $eventCategory->name,
					'color' => $eventCategoryColor
				)
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
	 *
	 * @deprecated 1.0.5 This function is no more needed
	 *
	 */
	public static function getStrToTimeEventDate($eventId = 0) {

		$eventId = intval($eventId);

		if ($eventId === 0) {
			return FALSE;
		}

		$eventDate = get_field(LA_Events_ACF::EVENT_START_DATE_FIELD, $eventId);

		$stringDate = strtotime($eventDate);

		return $stringDate;

	}

	/**
	 * Translate events object into calendar object
	 *
	 * @param array $eventsObject Array with events
	 *
	 * @return array|bool Array with events formatted into calendar request, or false if events object is empty
	 *
	 * @since 1.0.1 Added all-day parameter to convert from event object
	 * @since 1.0.5 Added ending parameter into calendar object
	 *              Added ID, content and category as event extra values
	 */
	public static function convertEventsObjectIntoCalendarObject($eventsObject = array()) {

		if (empty($eventsObject)) {
			return FALSE;
		}

		$calendarObject = array();

		foreach ($eventsObject as $eventObject) {

			array_push($calendarObject, array(
				'title' => $eventObject['title'],
				'start' => $eventObject['event_date'],
				'end' => $eventObject['event_end_date'],
				'allDay' => $eventObject['all_day'],
				'backgroundColor' => $eventObject['category']['color'],
				'extra' => array(
					'id' => $eventObject['ID'],
					'content' => $eventObject['content']
				)
			));

		}

		return $calendarObject;

	}

	/**
	 * Get all event categories
	 *
	 * @return array Array with categories
	 *
	 * @since 1.0.5 Added category color
	 */
	public static function getEventsCategories() {

		$categories = get_terms(array(
			'taxonomy' => LA_Events_Core::EVENT_POST_TYPE_CATEGORY,
			'hide_empty' => FALSE
		));

		$categoriesObject = array();

		if (!empty($categories)) {
			foreach ($categories as $category) {

				$categoryColor = LA_Events_Core::DEFAULT_EVENT_CATEGORY_COLOR;

				$categoryColorMeta = get_term_meta($category->term_id, 'color', TRUE);

				if (!empty($categoryColorMeta)) {
					$categoryColor = $categoryColorMeta;
				}

				array_push($categoriesObject, array(
					'ID' => $category->term_id,
					'name' => $category->name,
					'slug' => $category->slug,
					'color' => $categoryColor
				));
			}
		}

		return $categoriesObject;
	}

}