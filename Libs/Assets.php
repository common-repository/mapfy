<?php

namespace MAPFY\Libs;

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Assets')) {

	/**
	 * Assets Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 * @version     1.0.0
	 */
	class Assets
	{

		/**
		 * Constructor method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct()
		{
			add_action('admin_enqueue_scripts', array($this, 'mapfy_admin_enqueue_scripts'), 100);

			if (Helper::is_elementor_activated()) {
				add_action('elementor/frontend/before_register_scripts', [$this, 'register_script']);

				add_action('elementor/editor/after_enqueue_scripts', [$this, 'mapfy_enqueue_editor_scripts']);
			}
		}

		/**
		 * Editor Scripts
		 *
		 * @return void
		 */
		public function mapfy_enqueue_editor_scripts()
		{
?>
			<style>
				.elementor-element [class*="mapfy-icon"]:after {
					background: transparent;
					border-width: 0 0 1px 1px;
					border-color: #e0e0e0;
					border-style: solid;
					content: "Mapfy";
					font-family: Roboto, Arial, Helvetica, Verdana, sans-serif;
					font-size: 9px;
					position: absolute;
					top: 0;
					right: 0;
					padding: 0.2em 0.5em;
				}

				#elementor-panel-elements-wrapper .elementor-element:hover .mapfy-icon:after {
					background: #f50b7f;
					border-color: #f50b7f;
					color: #fff;
				}
			</style>
<?php
		}


		/**
		 * Register Elementor Script
		 *
		 * @return void
		 */
		public function register_script()
		{
			wp_register_script('mapfy-elementor', MAPFY_ASSETS . 'admin/js/mapfy-elementor.js', ['elementor-frontend'], MAPFY_VER, true);
		}

		/**
		 * Get environment mode
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_mode()
		{
			return defined('WP_DEBUG') && WP_DEBUG ? 'development' : 'production';
		}


		/**
		 * Enqueue Scripts
		 *
		 * @method admin_enqueue_scripts()
		 */
		public function mapfy_admin_enqueue_scripts()
		{
			// CSS Files .
			wp_enqueue_style('mapfy-admin', MAPFY_ASSETS . 'admin/css/mapfy-admin.min.css', array('dashicons'), MAPFY_VER, 'all');

			// JS Files .
			wp_enqueue_script('mapfy-admin', MAPFY_ASSETS . 'admin/js/mapfy-admin.min.js', array('jquery'), MAPFY_VER, true);
			wp_localize_script(
				'mapfy-admin',
				'MAPFYCORE',
				array(
					'admin_ajax'        => admin_url('admin-ajax.php'),
					'recommended_nonce' => wp_create_nonce('mapfy_recommended_nonce')
				)
			);
		}
	}
}