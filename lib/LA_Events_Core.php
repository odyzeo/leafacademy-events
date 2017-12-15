<?php

/**
 * Class LA_Events_Core
 *
 * @since 1.0.0
 */
class LA_Events_Core {

	const EVENT_POST_TYPE = 'la-event';
	const EVENT_POST_TYPE_CATEGORY = 'la-event-category';
	const TEXT_DOMAIN = 'la-events-calendar';

	/**
	 * Initialization
	 */
	public static function init() {

		add_action('init', array(__CLASS__, 'registerPostTypes'));
		add_action('init', array(__CLASS__, 'registerTaxonomies'));
		add_action('plugins_loaded', array(__CLASS__, 'loadTextDomain'));

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

}