<?php

/*
 * Plugin Name: Leaf Academy Events Calendar
 * Plugin URI: http://www.devstudio.sk
 * Description: TBA
 * Version: 1.0.3
 * Author: Dev Studio spol. s r.o.
 * Author URI: http://www.devstudio.sk
 * Text Domain: la-events-calendar
 */

//  work out plugin folder name and store it as a constant
$plugin_dir = str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
$plugin_dir = substr($plugin_dir, 0, strlen($plugin_dir) - 1);
define('LA_EVENTS_PATH', plugin_dir_path(__FILE__));
define('LA_EVENTS_DIR', $plugin_dir);
define('LA_EVENTS_INDEX', __FILE__);

require_once 'lib' . DIRECTORY_SEPARATOR . 'LA_Events_ACF.php';
require_once 'lib' . DIRECTORY_SEPARATOR . 'LA_Events_Core.php';
require_once 'lib' . DIRECTORY_SEPARATOR . 'LA_Events_Helper.php';
require_once 'lib' . DIRECTORY_SEPARATOR . 'LA_Events_REST.php';
require_once 'lib' . DIRECTORY_SEPARATOR . 'LA_Events_Shortcodes.php';

LA_Events_ACF::init();
LA_Events_Core::init();
LA_Events_REST::init();
LA_Events_Shortcodes::init();


