<?php

/**
 * Class LA_Events_Core
 *
 * @since 1.0.0
 */
class LA_Events_Core {

	const EVENT_POST_TYPE = 'la-event';
	const TEXT_DOMAIN = 'la-events-calendar';

	/**
	 * Initialization
	 */
	public static function init() {

		add_action('init', array(__CLASS__, 'registerPostTypes'));
		add_action('plugins_loaded', array(__CLASS__, 'loadTextDomain'));

	}

	/**
	 * Register Custom Post Types
	 */
	public static function registerPostTypes() {

		$labels = array(
			'name' => __('Events', 'Post Type General Name', 'la-events-calendar'),
			'singular_name' => __('Event', 'Post Type Singular Name', 'la-events-calendar'),
			'menu_name' => __('Events', 'la-events-calendar'),
			'name_admin_bar' => __('Event', 'la-events-calendar'),
			'archives' => __('Event Archives', 'la-events-calendar'),
			'attributes' => __('Event Attributes', 'la-events-calendar'),
			'parent_item_colon' => __('Parent Event:', 'la-events-calendar'),
			'all_items' => __('All Events', 'la-events-calendar'),
			'add_new_item' => __('Add New Event', 'la-events-calendar'),
			'add_new' => __('Add New', 'la-events-calendar'),
			'new_item' => __('New Event', 'la-events-calendar'),
			'edit_item' => __('Edit Event', 'la-events-calendar'),
			'update_item' => __('Update Event', 'la-events-calendar'),
			'view_item' => __('View Event', 'la-events-calendar'),
			'view_items' => __('View Events', 'la-events-calendar'),
			'search_items' => __('Search Event', 'la-events-calendar'),
			'not_found' => __('Not found', 'la-events-calendar'),
			'not_found_in_trash' => __('Not found in Trash', 'la-events-calendar'),
			'featured_image' => __('Featured Image', 'la-events-calendar'),
			'set_featured_image' => __('Set featured image', 'la-events-calendar'),
			'remove_featured_image' => __('Remove featured image', 'la-events-calendar'),
			'use_featured_image' => __('Use as featured image', 'la-events-calendar'),
			'insert_into_item' => __('Insert into Event', 'la-events-calendar'),
			'uploaded_to_this_item' => __('Uploaded to this Event', 'la-events-calendar'),
			'items_list' => __('Events list', 'la-events-calendar'),
			'items_list_navigation' => __('Events list navigation', 'la-events-calendar'),
			'filter_items_list' => __('Filter Events list', 'la-events-calendar'),
		);
		$args = array(
			'label' => __('Event', 'la-events-calendar'),
			'description' => __('Events', 'la-events-calendar'),
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
	 * Load translations text-domain
	 */
	public static function loadTextDomain() {

		$translationsDir = LA_EVENTS_PATH . 'i18n';

		load_plugin_textdomain(self::TEXT_DOMAIN, FALSE, $translationsDir);
	}

}