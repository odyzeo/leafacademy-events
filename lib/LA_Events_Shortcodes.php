<?php

/**
 * Class LA_Events_Shortcodes
 *
 * @since 1.0.0
 */
class LA_Events_Shortcodes {

	const SHOW_CALENDAR_SHORTCODE = 'ls-events-calendar';

	public static $isCalendarVisible = FALSE;

	/**
	 * Initialization
	 */
	public static function init() {

		self::registerShortcodes();

		add_action('wp_footer', array(__CLASS__, 'wpFooter'));

	}

	/**
	 * Register all shortcodes
	 */
	public static function registerShortcodes() {

		add_shortcode(self::SHOW_CALENDAR_SHORTCODE, array(__CLASS__, 'showEventsCalendar'));

	}

	/**
	 * Render calendar shortcode
	 *
	 * @param $atts array Shortcode attributes
	 */
	public static function showEventsCalendar($atts) {

		self::$isCalendarVisible = TRUE;

		$events = LA_Events_Helper::buildEventsQuery();
		$eventObject = LA_Events_Helper::buildEventsObject($events);

		echo '<pre>';
		print_r($eventObject);
		echo '</pre>';

		?>
		<div id="la-calendar"></div>
		<?php
	}

	/**
	 * Actions to be fired when footer is rendered
	 */
	public static function wpFooter() {

		if (self::$isCalendarVisible) {
			wp_enqueue_script('moment-with-locales-js', plugins_url('/js/moment-with-locales.min.js', LA_EVENTS_INDEX), array('jquery'), '1.0.0', FALSE);
			wp_enqueue_script('fullcalendar-js', plugins_url('/js/fullcalendar.min.js', LA_EVENTS_INDEX), array('moment-with-locales-js'), '1.0.0', FALSE);
			wp_enqueue_script('la-events-calendar-js', plugins_url('/js/la-events-calendar.min.js', LA_EVENTS_INDEX), array('fullcalendar-js'), '1.0.0', FALSE);
			wp_localize_script('la-events-calendar-js', 'LA_Events', array(
				'rest_url' => rest_url()
			));
			?>
			<script>
				jQuery.noConflict();
				(function($) {
					$(function() {
						$(document).ready(function() {
							jQuery('head').append('<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/fullcalendar.min.css?v=1.0.0', LA_EVENTS_INDEX); ?>">');
						});
					});
				})(jQuery);
			</script>
			<?php
		}

	}

}