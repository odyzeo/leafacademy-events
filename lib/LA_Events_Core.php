<?php

/**
 * Class LA_Events_Core
 *
 * @since 1.0.0
 * @since 1.0.5 Added category default color
 */
class LA_Events_Core {

	const EVENT_POST_TYPE = 'la-event';
	const EVENT_POST_TYPE_CATEGORY = 'la-event-category';
	const TEXT_DOMAIN = 'la-events-calendar';
	const DEFAULT_EVENT_CATEGORY_COLOR = '#EFEFEF';
	const DEFAULT_EVENT_PER_PAGE = 10;

	/**
	 * Initialization
	 *
	 * @since 1.0.6 Added wp_footer action
	 * @since 1.0.7 Added save_post action
	 */
	public static function init() {

		add_action('init', array(__CLASS__, 'registerPostTypes'));
		add_action('init', array(__CLASS__, 'registerTaxonomies'));
		add_action('plugins_loaded', array(__CLASS__, 'loadTextDomain'));
		add_action('wp_footer', array(__CLASS__, 'wpFooter'));
		add_action('save_post', array(__CLASS__, 'saveDateOnPostSave'));

	}

	/**
	 * Register Custom Post Types
	 */
	public static function registerPostTypes() {

		$labels = array(
			'name' => __('Events', 'Post Type General Name', self::TEXT_DOMAIN),
			'singular_name' => __('Event', 'Post Type Singular Name', self::TEXT_DOMAIN),
			'menu_name' => __('Events', self::TEXT_DOMAIN),
			'name_admin_bar' => __('Event', self::TEXT_DOMAIN),
			'archives' => __('Event Archives', self::TEXT_DOMAIN),
			'attributes' => __('Event Attributes', self::TEXT_DOMAIN),
			'parent_item_colon' => __('Parent Event:', self::TEXT_DOMAIN),
			'all_items' => __('All Events', self::TEXT_DOMAIN),
			'add_new_item' => __('Add New Event', self::TEXT_DOMAIN),
			'add_new' => __('Add New', self::TEXT_DOMAIN),
			'new_item' => __('New Event', self::TEXT_DOMAIN),
			'edit_item' => __('Edit Event', self::TEXT_DOMAIN),
			'update_item' => __('Update Event', self::TEXT_DOMAIN),
			'view_item' => __('View Event', self::TEXT_DOMAIN),
			'view_items' => __('View Events', self::TEXT_DOMAIN),
			'search_items' => __('Search Event', self::TEXT_DOMAIN),
			'not_found' => __('Not found', self::TEXT_DOMAIN),
			'not_found_in_trash' => __('Not found in Trash', self::TEXT_DOMAIN),
			'featured_image' => __('Featured Image', self::TEXT_DOMAIN),
			'set_featured_image' => __('Set featured image', self::TEXT_DOMAIN),
			'remove_featured_image' => __('Remove featured image', self::TEXT_DOMAIN),
			'use_featured_image' => __('Use as featured image', self::TEXT_DOMAIN),
			'insert_into_item' => __('Insert into Event', self::TEXT_DOMAIN),
			'uploaded_to_this_item' => __('Uploaded to this Event', self::TEXT_DOMAIN),
			'items_list' => __('Events list', self::TEXT_DOMAIN),
			'items_list_navigation' => __('Events list navigation', self::TEXT_DOMAIN),
			'filter_items_list' => __('Filter Events list', self::TEXT_DOMAIN),
		);
		$args = array(
			'label' => __('Event', self::TEXT_DOMAIN),
			'description' => __('Events', self::TEXT_DOMAIN),
			'labels' => $labels,
			'menu_icon' => 'dashicons-calendar',
			'supports' => array('title', 'editor', 'custom-fields',),
			'taxonomies' => array(),
			'public' => TRUE,
			'show_ui' => TRUE,
			'show_in_menu' => TRUE,
			'menu_position' => 5,
			'show_in_admin_bar' => TRUE,
			'show_in_nav_menus' => TRUE,
			'can_export' => TRUE,
			'has_archive' => FALSE,
			'hierarchical' => FALSE,
			'exclude_from_search' => FALSE,
			'show_in_rest' => TRUE,
			'publicly_queryable' => TRUE,
			'capability_type' => 'post',
		);
		register_post_type(self::EVENT_POST_TYPE, $args);

	}

	/**
	 * Create taxonomies depending on events
	 */
	public static function registerTaxonomies() {

		$labels = array(
			'name' => _x('Categories', 'taxonomy general name', self::TEXT_DOMAIN),
			'singular_name' => _x('Category', 'taxonomy singular name', self::TEXT_DOMAIN),
			'search_items' => __('Search Categories', self::TEXT_DOMAIN),
			'all_items' => __('All Categories', self::TEXT_DOMAIN),
			'parent_item' => __('Parent Category', self::TEXT_DOMAIN),
			'parent_item_colon' => __('Parent Category:', self::TEXT_DOMAIN),
			'edit_item' => __('Edit Category', self::TEXT_DOMAIN),
			'update_item' => __('Update Category', self::TEXT_DOMAIN),
			'add_new_item' => __('Add New Category', self::TEXT_DOMAIN),
			'new_item_name' => __('New Category Name', self::TEXT_DOMAIN),
			'menu_name' => __('Category', self::TEXT_DOMAIN),
		);
		$args = array(
			'labels' => $labels,
			'description' => __('', self::TEXT_DOMAIN),
			'hierarchical' => TRUE,
			'public' => TRUE,
			'publicly_queryable' => TRUE,
			'show_ui' => TRUE,
			'show_in_menu' => TRUE,
			'show_in_nav_menus' => TRUE,
			'show_in_rest' => FALSE,
			'show_tagcloud' => TRUE,
			'show_in_quick_edit' => TRUE,
			'show_admin_column' => FALSE,
		);
		register_taxonomy(self::EVENT_POST_TYPE_CATEGORY, array(self::EVENT_POST_TYPE,), $args);
	}

	/**
	 * Load translations text-domain
	 */
	public static function loadTextDomain() {

		$translationsDir = LA_EVENTS_PATH . 'i18n';

		load_plugin_textdomain(self::TEXT_DOMAIN, FALSE, $translationsDir);
	}

	/**
	 * Save general date parameter into database when post is saved
	 *
	 * @param int $postId Post ID
	 *
	 * @return bool False if Post ID is zero
	 *
	 * @since 1.0.7
	 */
	public static function saveDateOnPostSave($postId = 0) {

		$postId = intval($postId);

		if ($postId === 0) {
			return FALSE;
		}

		$isEventAllDay = get_field(LA_Events_ACF::EVENT_ALL_DAY_FIELD, $postId);

		if ($isEventAllDay) {
			$eventGeneralDate = get_field(LA_Events_ACF::EVENT_START_DATE_FIELD, $postId);
		} else {
			$eventGeneralDate = get_field(LA_Events_ACF::EVENT_START_DATE_TIME_FIELD, $postId);
		}

		$eventGeneralDateFormatted = new DateTime($eventGeneralDate);
		$eventGeneralDateValue = $eventGeneralDateFormatted->format('Ymd');

		update_post_meta($postId, LA_Events_ACF::EVENT_GENERAL_DATE, $eventGeneralDateValue);

	}

	/**
	 * Action firing in footer
	 *
	 * @since 1.6.0
	 */
	public static function wpFooter() {

		if (LA_Events_Shortcodes::$isCalendarVisible) {
			?>
			<script id="event-item" type="text/x-handlebars-template">
				{{#each items}}
				<div class="row item middle-xs tippy" data-tippy-arrow="true" data-tippy-animation="shift-toward" data-tippy-duration="[600,300]" data-id="{{ID}}" data-tipp-trigger="mouseenter">
					<div class="col-xs-12 heading">
						<div class="row">
							<div class="col-xs-9 date">
								{{#if all_day}}
								{{date_object.start_date}} - {{date_object.end_date}}
								{{else}}
								{{date_object.start_date}} {{date_object.start_time}} - {{date_object.end_date}} {{date_object.end_time}}
								{{/if}}
							</div>
							<div class="col-xs-3 extra">
								{{#if all_day}}
								<?php _e('All day', self::TEXT_DOMAIN); ?>
								{{/if}}
							</div>
						</div>
					</div>
					<div class="col-xs-12 data">
						<div class="row middle-xs">
							<div class="col-xs-1">
								<div class="category_marker" style="background-color:{{category.color}};"></div>
							</div>
							<div class="col-xs-11 title">{{title}}</div>
						</div>
					</div>
				</div>
				{{/each}}
			</script>
			<?php
		}

	}

}