<?php

/**
 * Class LA_Events_Shortcodes
 *
 * @since 1.0.0
 */
class LA_Events_Shortcodes {

	const SHOW_CALENDAR_SHORTCODE = 'la-events-calendar';

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

		$eventCategories = LA_Events_Helper::getEventsCategories();

		?>
		<form id="calendar-helper">
			<input hidden type="hidden" name="start_interval" value="0">
			<input hidden type="hidden" name="end_interval" value="0">
			<input hidden type="hidden" name="per_page" value="<?php echo LA_Events_Core::DEFAULT_EVENT_PER_PAGE; ?>">
			<input hidden type="hidden" name="page" value="0">
			<input hidden type="hidden" name="category" value="0">
		</form>
		<div class="la-calendar-wrapper">
			<div class="row">
				<div class="col-xs-12 col-sm-8 fc-wrapper">
					<div id="la-calendar"></div>
				</div>
				<div class="col-xs-12 col-sm-4 categories">
					<div class="desktop-view">
						<label for="event_category"><?php _e('Categories', LA_Events_Core::TEXT_DOMAIN); ?></label>
						<ul id="event_category">
							<li style="border-left:5px solid <?php echo LA_Events_Core::DEFAULT_EVENT_CATEGORY_COLOR; ?>" class="item active" data-id="0"><?php _e('All categories', LA_Events_Core::TEXT_DOMAIN); ?></li>
							<?php foreach ($eventCategories as $category): ?>
								<li style="border-left:5px solid <?php echo $category['color']; ?>" class="item" data-id="<?php echo $category['ID']; ?>"><?php echo $category['name']; ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="mobile-view">
						<label for="event_category"><?php _e('Categories', LA_Events_Core::TEXT_DOMAIN); ?></label>
						<select name="event_category">
							<option value="0"><?php _e('All categories', LA_Events_Core::TEXT_DOMAIN); ?></option>
							<?php foreach ($eventCategories as $category): ?>
								<option value="<?php echo $category['ID']; ?>"><?php echo $category['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="la-events-listing">
			<div class="row">
				<div class="col-xs-12 categories">
					<div class="mobile-view">
						<label for="event_category"><?php _e('Categories', LA_Events_Core::TEXT_DOMAIN); ?></label>
						<select name="event_category">
							<option value="0"><?php _e('All categories', LA_Events_Core::TEXT_DOMAIN); ?></option>
							<?php foreach ($eventCategories as $category): ?>
								<option value="<?php echo $category['ID']; ?>"><?php echo $category['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="col-xs 12 listing">

				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Actions to be fired when footer is rendered
	 */
	public static function wpFooter() {

		if (self::$isCalendarVisible) {
			wp_enqueue_script('moment-with-locales-js', plugins_url('/js/moment-with-locales.min.js', LA_EVENTS_INDEX), array('jquery'), '1.0.0', FALSE);
			wp_enqueue_script('fullcalendar-js', plugins_url('/js/fullcalendar.min.js', LA_EVENTS_INDEX), array('moment-with-locales-js'), '1.0.0', FALSE);
			wp_enqueue_script('loadingoverlay-js', plugins_url('/js/loadingoverlay.min.js', LA_EVENTS_INDEX), array('fullcalendar-js'), '1.0.0', FALSE);
			wp_enqueue_script('loadingoverlay-js-extra', plugins_url('/js/loadingoverlay_progress.min.js', LA_EVENTS_INDEX), array('fullcalendar-js'), '1.0.0', FALSE);
			wp_enqueue_script('tippy-js', plugins_url('/js/tippy.all.min.js', LA_EVENTS_INDEX), array('loadingoverlay-js-extra'), '1.0.0', FALSE);
			wp_enqueue_script('handlebars-runtime-js', plugins_url('/js/handlebars.runtime.min.js', LA_EVENTS_INDEX), array('jquery'), '1.0.0', FALSE);
			wp_enqueue_script('handlebars-js', plugins_url('/js/handlebars.min.js', LA_EVENTS_INDEX), array('handlebars-runtime-js'), '1.0.0', FALSE);
			wp_enqueue_script('la-events-calendar-js', plugins_url('/js/la-events-calendar.min.js', LA_EVENTS_INDEX), array('handlebars-js'), '1.0.3', FALSE);
			wp_localize_script('la-events-calendar-js', 'LA_Events', array(
				'rest_url' => rest_url(),
				'mobile_rest_url' => rest_url() . 'la-events-calendar/v1/event-mobile/',
				'event_start' => __('Event start :', LA_Events_Core::TEXT_DOMAIN),
				'all_day' => __('All day', LA_Events_Core::TEXT_DOMAIN),
				'load_more' => __('Load more', LA_Events_Core::TEXT_DOMAIN)
			));
			?>
			<script>
				jQuery.noConflict();
				(function($) {
					$(function() {
						$(document).ready(function() {
							jQuery('head').append('<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/fullcalendar.min.css?v=1.0.0', LA_EVENTS_INDEX); ?>">');
							jQuery('head').append('<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/la-events-calendar.min.css?v=1.0.3', LA_EVENTS_INDEX); ?>">');
							jQuery('head').append('<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/css/flexboxgrid.min.css?v=1.0.0', LA_EVENTS_INDEX); ?>">');
						});
					});
				})(jQuery);
			</script>
			<?php
		}

	}

}