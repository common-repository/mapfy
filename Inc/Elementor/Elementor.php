<?php

namespace MAPFY\Inc\Elementor;

use MAPFY\Libs\Helper;

if (!class_exists('Elementor')) {
    /**
     * Class for Elementor Page Builder
     *
     * Jewel Theme <support@jeweltheme.com>
     */
    class Elementor
    {
        const MINIMUM_PHP_VERSION = '5.4';
        const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

        /**
         * Construct method
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function __construct()
        {
            add_action('plugins_loaded', [$this, 'mapfy_plugins_loaded']);

            // Add Elementor Widgets
            add_action('elementor/widgets/widgets_registered', [$this, 'mapfy_init_widgets']);
        }

        public function mapfy_init_widgets()
        {
            \Elementor\Plugin::instance()->widgets_manager->register(new Elementor_Mapfy());
        }

        public function mapfy_plugins_loaded()
        {

            // Check if Elementor installed and activated
            if (!did_action('elementor/loaded')) {
                add_action('admin_notices', array($this, 'mapfy_admin_notice_missing_main_plugin'));
                return;
            }

            // Check for required Elementor version
            if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
                add_action('admin_notices', array($this, 'mapfy_admin_notice_minimum_elementor_version'));
                return;
            }

            // Check for required PHP version
            if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
                add_action('admin_notices', array($this, 'mapfy_admin_notice_minimum_php_version'));
                return;
            }
        }



        public function mapfy_admin_notice_missing_main_plugin()
        {
            $plugin = 'elementor/elementor.php';

            if (Helper::is_elementor_activated()) {
                if (!current_user_can('activate_plugins')) {
                    return;
                }
                $activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin);
                $message = sprintf('<b>Mapfy</b> requires <b>Elementor</b> plugin to be active. Please activate Elementor to continue.', 'mapfy');
                $button_text = esc_html__('Activate Elementor', 'mapfy');
            } else {
                if (!current_user_can('install_plugins')) {
                    return;
                }

                $activation_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
                $message = sprintf(__('<b>Mapfy</b> requires %1$s"Elementor"%2$s plugin to be installed and activated. Please install Elementor to continue.', 'mapfy'), '<strong>', '</strong>');
                $button_text = esc_html__('Install Elementor', 'mapfy');
            }




            $button = '<p><a href="' . esc_url_raw($activation_url) . '" class="button-primary">' . esc_html($button_text) . '</a></p>';

            printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p>%2$s</div>', esc_html($message), esc_html($button));
        }

        public function mapfy_admin_notice_minimum_elementor_version()
        {
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }

            $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
                esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'mapfy'),
                '<strong>' . esc_html__('Mapfy ', 'mapfy') . '</strong>',
                '<strong>' . esc_html__('Elementor', 'mapfy') . '</strong>',
                self::MINIMUM_ELEMENTOR_VERSION
            );

            printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html( $message ) );
        }

        public function mapfy_admin_notice_minimum_php_version()
        {
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }

            $message = sprintf(
                /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
                esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'mapfy'),
                '<strong>' . esc_html__('Mapfy ', 'mapfy') . '</strong>',
                '<strong>' . esc_html__('PHP', 'mapfy') . '</strong>',
                self::MINIMUM_PHP_VERSION
            );

            printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html( $message ));
        }
    }
}