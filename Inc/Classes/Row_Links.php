<?php

namespace MAPFY\Inc\Classes;

use MAPFY\Libs\RowLinks;

if (!class_exists('Row_Links')) {
	/**
	 * Row Links Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 */
	class Row_Links extends RowLinks
	{

		public $is_active;
		public $is_free;

		/**
		 * Construct method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct()
		{
			parent::__construct();

			$this->is_active = false;
			$this->is_free   = true;
		}


		/**
		 * Plugin action links
		 *
		 * @param [type] $links .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function plugin_action_links($links)
		{
			$links[] = sprintf(
				'<a href="%1$s" target="_blank">%2$s</a>',
				'https://jeweltheme.com/docs/mapfy/mapfy-plugin',
				__('Docs', 'mapfy')
			);
			$links[] = sprintf(
				'<a href="%1$s" target="_blank">%2$s</a>',
				'https://jeweltheme.com/contact',
				__('Support', 'mapfy')
			);
			if ($this->is_free) {
				$links[] = sprintf(
					'<a href="%1$s" target="_blank">%2$s</a>',
					'https://jeweltheme.com/mapfy#pricing_plan',
					__('Upgrade Pro', 'mapfy')
				);
			}
			return $links;
		}
	}
}