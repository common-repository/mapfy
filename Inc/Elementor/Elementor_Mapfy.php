<?php

namespace MAPFY\Inc\Elementor;

use MAPFY\Libs\Helper;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Elementor_Mapfy extends Widget_Base
{

    public function get_name()
    {
        return 'mapfy-leaflet-map';
    }

    public function get_title()
    {
        return esc_html__(' Open Street Map', 'mapfy');
    }

    public function get_icon()
    {
        return 'eicon-google-maps mapfy-icon';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_keywords()
    {
        return ['open', 'street', 'map', 'location'];
    }

    public function get_style_depends()
    {
        // if (Helper::is_elementor_edit_mode()) {
            return ['mapfy-leaflet'];
        // } else {
        //     return ['mapfy-leaflet'];
        // }
    }

    public function get_script_depends()
    {
        // if (Helper::is_elementor_edit_mode()) {
            return ['mapfy-leaflet', 'mapfy-elementor', 'mapfy-scripts', 'mapfy-leaflet-fullscreen'];
        // } else {
        //     return ['mapfy-leaflet', 'mapfy-scripts'];
        // }
    }

    // public function get_custom_help_url()
    // {
    //     return 'https://youtu.be/{tutorials_url}';
    // }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_content_osmap',
            [
                'label' => esc_html__('Open Street Map', 'mapfy'),
            ]
        );

        $this->add_control(
            'zoom_control',
            [
                'label'   => esc_html__('Zoom Control', 'mapfy'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'zoom',
            [
                'label' => esc_html__('Zoom', 'mapfy'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'open_street_map_height',
            [
                'label' => esc_html__('Map Height', 'mapfy'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mapfy-open-street-map'  => 'min-height: {{SIZE}}{{UNIT}} !important',
                ],
            ]
        );

        $this->add_responsive_control(
            'search_align',
            [
                'label'   => esc_html__('Alignment', 'mapfy'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'mapfy'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'mapfy'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'mapfy'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mapfy-osmap-search-wrapper' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'osmap_geocode' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'search_spacing',
            [
                'label' => esc_html__('Spacing', 'mapfy'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mapfy-osmap-search-wrapper'  => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'osmap_geocode' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_style_css_filters');

        $this->start_controls_tab(
            'tab_css_filter_normal',
            [
                'label' => esc_html__('Normal', 'mapfy')
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .mapfy-open-street-map',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'map_border',
                'label'    => esc_html__('Border', 'mapfy'),
                'selector' => '{{WRAPPER}} .mapfy-open-street-map',
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'map_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'mapfy'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .mapfy-open-street-map' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_css_filter_hover',
            [
                'label' => esc_html__('Hover', 'mapfy')
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .mapfy-open-street-map:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_marker',
            [
                'label' => esc_html__('Marker', 'mapfy'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'marker_title',
            [
                'label'   => esc_html__('Title', 'mapfy'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Marker #1',
                'dynamic'     => ['active' => true,],
            ]
        );

        $repeater->add_control(
            'marker_lat',
            [
                'label' => esc_html__('Latitude', 'mapfy'),
                'type'  => Controls_Manager::TEXT,
                'default' => '24.82391',
                'dynamic'     => ['active' => true,],
            ]
        );

        $repeater->add_control(
            'marker_lng',
            [
                'label'   => esc_html__('Longitude', 'mapfy'),
                'type'    => Controls_Manager::TEXT,
                'default' => '89.38414',
                'dynamic'     => ['active' => true,],
            ]
        );

        $repeater->add_control(
            'marker_content',
            [
                'label'   => esc_html__('Content', 'mapfy'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Your Business Address Here', 'mapfy'),
                // 'dynamic'     => ['active' => true,],
            ]
        );

        $repeater->add_control(
            'custom_marker',
            [
                'label'       => esc_html__('Custom marker', 'mapfy'),
                'description' => esc_html__('Use 25x41 px size png icon for exact point.', 'mapfy'),
                'type'        => Controls_Manager::MEDIA,
                'dynamic'     => ['active' => true,],
            ]
        );

        $this->add_control(
            'markers',
            [
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'marker_lat'     => '24.82391',
                        'marker_lng'     => '89.38414',
                        'marker_title'   => esc_html__('Marker #1', 'mapfy'),
                        'marker_content' => esc_html__('<strong>Pixar Labs</strong>,<br>Mirpur DOHS, Dhaka,<br>Bangladesh', 'mapfy'),
                    ],
                ],
                'title_field' => '{{{ marker_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_tooltip',
            [
                'label' => esc_html__('Tooltip', 'mapfy'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'marker_tooltip_color',
            [
                'label'     => esc_html__('Text Color', 'mapfy'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .leaflet-popup-content-wrapper' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'marker_tooltip_button_color',
            [
                'label'     => esc_html__('Close Button Color', 'mapfy'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .leaflet-popup-close-button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'marker_tooltip_button_hover_color',
            [
                'label'     => esc_html__('Close Button Hover Color', 'mapfy'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .leaflet-popup-close-button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'marker_tooltip_background',
                'selector' => '{{WRAPPER}} .leaflet-popup-content-wrapper, {{WRAPPER}} .leaflet-popup-tip',
            ]
        );

        $this->add_responsive_control(
            'marker_tooltip_padding',
            [
                'label'      => esc_html__('Padding', 'mapfy'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .leaflet-popup-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'marker_tooltip_border',
                'label'       => esc_html__('Border', 'mapfy'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .leaflet-popup-content-wrapper',
            ]
        );

        $this->add_responsive_control(
            'marker_tooltip_border_radius',
            [
                'label'      => esc_html__('Radius', 'mapfy'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .leaflet-popup-content-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'marker_tooltip_shadow',
                'selector' => '{{WRAPPER}} .leaflet-popup-content-wrapper',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'marker_tooltip_typography',
                'selector' => '{{WRAPPER}} .leaflet-popup-content-wrapper',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_zoom_control',
            [
                'label'     => esc_html__('Zoom Control', 'mapfy'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'zoom_control' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'zoom_control_color',
            [
                'label'     => esc_html__('Icon Color', 'mapfy'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mapfy-open-street-map .leaflet-bar a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'zoom_control_background',
                'selector' => '{{WRAPPER}} .mapfy-open-street-map .leaflet-bar a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'zoom_control_border',
                'label'       => esc_html__('Border', 'mapfy'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .mapfy-open-street-map .leaflet-bar a',
            ]
        );

        $this->add_responsive_control(
            'zoom_control_border_radius',
            [
                'label'      => esc_html__('Radius', 'mapfy'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .mapfy-open-street-map .leaflet-bar a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'zoom_control_bar_color',
            [
                'label'     => esc_html__('Bar Color', 'mapfy'),
                'type'      => Controls_Manager::COLOR,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .mapfy-open-street-map .leaflet-bar' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'zoom_control_bar_width',
            [
                'label'     => esc_html__('Bar Width', 'mapfy'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .mapfy-open-street-map .leaflet-bar' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $mapfy_api_settings = get_option('element_pack_api_settings');

        $marker_settings = [];
        $map_settings    = [];
        $mapfy_counter     = 0;

        $map_settings['mapboxToken'] = isset($mapfy_api_settings['open_street_map_access_token']) ? $mapfy_api_settings['open_street_map_access_token'] : false;

        foreach ($settings['markers'] as $marker_item) {

            $marker_settings['lat']        = ($marker_item['marker_lat']) ? $marker_item['marker_lat'] : '';
            $marker_settings['lng']        = ($marker_item['marker_lng']) ? $marker_item['marker_lng'] : '';
            $marker_settings['title']      = ($marker_item['marker_title']) ? $marker_item['marker_title'] : '';
            $marker_settings['iconUrl']    = ($marker_item['custom_marker']['url']) ? $marker_item['custom_marker']['url'] : '';
            $marker_settings['infoWindow'] = ($marker_item['marker_content']) ? $marker_item['marker_content'] : '';

            $all_markers[] = $marker_settings;

            $mapfy_counter++;

            if (1 === $mapfy_counter) {
                $map_settings['lat'] = ($marker_item['marker_lat']) ? $marker_item['marker_lat'] : '';
                $map_settings['lng'] = ($marker_item['marker_lng']) ? $marker_item['marker_lng'] : '';
            }

            $map_settings['zoomControl'] = ($settings['zoom_control']) ? true : false;
            $map_settings['zoom'] = $settings['zoom']['size'];

        }

        $this->add_render_attribute('open-street-map', 'data-settings', wp_json_encode($map_settings));
        $this->add_render_attribute('open-street-map', 'data-map_markers', wp_json_encode($all_markers));


?>
        <div class="mapfy-open-street-map" style="width: auto; min-height: 400px;" <?php echo esc_attr( $this->get_render_attribute_string('open-street-map') ); ?>></div>
<?php
    }
}