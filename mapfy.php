<?php

/**
 * Plugin Name: Mapfy
 * Plugin URI:  https://jeweltheme.com
 * Description: WordPress Google Maps Plugin
 * Version:     1.0.1
 * Author:      Jewel Theme
 * Author URI:  https://jeweltheme.com/mapfy
 * Text Domain: mapfy
 * Domain Path: languages/
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package mapfy
 */

/*
 * don't call the file directly
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$mapfy_plugin_data = get_file_data(
	__FILE__,
	array(
		'Version'     => 'Version',
		'Plugin Name' => 'Plugin Name',
		'Author'      => 'Author',
		'Description' => 'Description',
		'Plugin URI'  => 'Plugin URI',
	),
	false
);

// Define Constants.
if (!defined('MAPFY')) {
	define('MAPFY', $mapfy_plugin_data['Plugin Name']);
}

if (!defined('MAPFY_VER')) {
	define('MAPFY_VER', $mapfy_plugin_data['Version']);
}

if (!defined('MAPFY_AUTHOR')) {
	define('MAPFY_AUTHOR', $mapfy_plugin_data['Author']);
}

if (!defined('MAPFY_DESC')) {
	define('MAPFY_DESC', $mapfy_plugin_data['Author']);
}

if (!defined('MAPFY_URI')) {
	define('MAPFY_URI', $mapfy_plugin_data['Plugin URI']);
}

if (!defined('MAPFY_DIR')) {
	define('MAPFY_DIR', __DIR__);
}

if (!defined('MAPFY_FILE')) {
	define('MAPFY_FILE', __FILE__);
}

if (!defined('MAPFY_SLUG')) {
	define('MAPFY_SLUG', dirname(plugin_basename(__FILE__)));
}

if (!defined('MAPFY_BASE')) {
	define('MAPFY_BASE', plugin_basename(__FILE__));
}

if (!defined('MAPFY_PATH')) {
	define('MAPFY_PATH', trailingslashit(plugin_dir_path(__FILE__)));
}

if (!defined('MAPFY_URL')) {
	define('MAPFY_URL', trailingslashit(plugins_url('/', __FILE__)));
}

if (!defined('MAPFY_INC')) {
	define('MAPFY_INC', MAPFY_PATH . '/Inc/');
}

if (!defined('MAPFY_LIBS')) {
	define('MAPFY_LIBS', MAPFY_PATH . 'Libs');
}

if (!defined('MAPFY_ASSETS')) {
	define('MAPFY_ASSETS', MAPFY_URL . 'assets/');
}

if (!defined('MAPFY_IMAGES')) {
	define('MAPFY_IMAGES', MAPFY_ASSETS . 'images/');
}

if (!class_exists('\\MAPFY\\Mapfy')) {
	// Autoload Files.
	include_once MAPFY_DIR . '/vendor/autoload.php';
	// Instantiate Mapfy Class.
	include_once MAPFY_DIR . '/class-mapfy.php';
}