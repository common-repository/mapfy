<?php

namespace MAPFY\Inc\Classes;

use MAPFY\Libs\Helper;
use MAPFY\Inc\Elementor\Elementor;
use MAPFY\Inc\Gutenberg\Gutenberg;

if (!class_exists('Admin')) {
    /**
     * Class for Admin Page Builder
     *
     * Jewel Theme <support@jeweltheme.com>
     */
    class Admin
    {

        /**
         * Construct method
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function __construct()
        {
            add_action('plugins_loaded', [$this, 'admin_init']);
        }


        /**
         * Admin Init method
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function admin_init()
        {
            // if (Helper::is_elementor_activated()) {
                new Elementor();
            // }
            new Gutenberg();
        }
    }
}