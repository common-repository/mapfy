<?php

namespace MAPFY;

use MAPFY\Libs\Assets;
use MAPFY\Libs\Helper;
use MAPFY\Inc\Classes\Notifications\Notifications;
use MAPFY\Inc\Classes\Admin;
use MAPFY\Inc\Classes\Row_Links;
use MAPFY\Inc\Classes\Upgrade_Plugin;
use MAPFY\Inc\Classes\Feedback;

/**
 * Main Class
 *
 * @Mapfy
 * Jewel Theme <support@jeweltheme.com>
 * @version     1.0.0
 */

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Mapfy Class
 */
if (!class_exists('\MAPFY\Mapfy')) {

	/**
	 * Class: Mapfy
	 */
	final class Mapfy
	{

		const VERSION            = MAPFY_VER;
		private static $instance = null;

		/**
		 * what we collect construct method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct()
		{
			$this->includes();
			add_action('plugins_loaded', array($this, 'mapfy_plugins_loaded'), 999);
			// Body Class.
			add_filter('admin_body_class', array($this, 'mapfy_body_class'));
		}

		/**
		 * plugins_loaded method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function mapfy_plugins_loaded()
		{
			$this->mapfy_activate();
		}

		/**
		 * Version Key
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function plugin_version_key()
		{
			return Helper::mapfy_slug_cleanup() . '_version';
		}

		/**
		 * Activation Hook
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function mapfy_activate()
		{
			$current_mapfy_version = get_option(self::plugin_version_key(), null);

			if (get_option('mapfy_activation_time') === false) {
				update_option('mapfy_activation_time', strtotime('now'));
			}

			if (is_null($current_mapfy_version)) {
				update_option(self::plugin_version_key(), self::VERSION);
			}

			$allowed = get_option(Helper::mapfy_slug_cleanup() . '_allow_tracking', 'no');

			// if it wasn't allowed before, do nothing .
			if ('yes' !== $allowed) {
				return;
			}
			// re-schedule and delete the last sent time so we could force send again .
			$hook_name = Helper::mapfy_slug_cleanup() . '_tracker_send_event';
			if (!wp_next_scheduled($hook_name)) {
				wp_schedule_event(time(), 'weekly', $hook_name);
			}
		}


		/**
		 * Add Body Class
		 *
		 * @param [type] $classes .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function mapfy_body_class($classes)
		{
			$classes .= ' mapfy ';
			return $classes;
		}

		/**
		 * Run Upgrader Class
		 *
		 * @return void
		 */
		public function mapfy_maybe_run_upgrades()
		{
			if (!is_admin() && !current_user_can('manage_options')) {
				return;
			}

			// Run Upgrader .
			$upgrade = new Upgrade_Plugin();

			// Need to work on Upgrade Class .
			if ($upgrade->if_updates_available()) {
				$upgrade->run_updates();
			}
		}

		/**
		 * Include methods
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function includes()
		{
			new Assets();
			new Admin();
			new Row_Links();
			new Notifications();
			new Feedback();
		}


		/**
		 * Initialization
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function mapfy_init()
		{
			$this->mapfy_load_textdomain();
		}


		/**
		 * Text Domain
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function mapfy_load_textdomain()
		{
			$domain = 'mapfy';
			$locale = apply_filters('mapfy_plugin_locale', get_locale(), $domain);

			load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
			load_plugin_textdomain($domain, false, dirname(MAPFY_BASE) . '/languages/');
		}

		/**
		 * Returns the singleton instance of the class.
		 */
		public static function get_instance()
		{
			if (!isset(self::$instance) && !(self::$instance instanceof Mapfy)) {
				self::$instance = new Mapfy();
				self::$instance->mapfy_init();
			}

			return self::$instance;
		}
	}

	// Get Instant of Mapfy Class .
	Mapfy::get_instance();
}