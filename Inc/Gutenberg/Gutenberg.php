<?php

namespace MAPFY\Inc\Gutenberg;

use MAPFY\Libs\Helper;

if (!class_exists('Gutenberg')) {
    /**
     * Class for Gutenberg Editor
     *
     * Jewel Theme <support@jeweltheme.com>
     */
    class Gutenberg
    {
        /**
         * Construct method
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function __construct()
        {
            add_action('enqueue_block_editor_assets', [$this, 'mapfy_enqueue_block_editor_assets']);
            add_action('init', array($this, 'mapfy_register_block'));
        }

        /**
         * Register Block Type
         *
         * @return void
         */
        public function mapfy_register_block()
        {
            // Blocks Init JS
            $asset_file = include(MAPFY_PATH . 'assets/blocks/mapfy/index.asset.php');
            wp_register_script('mapfy-block', MAPFY_ASSETS . 'index.js', $asset_file['dependencies'], $asset_file['version'], true);

            wp_register_style('mapfy-editor', MAPFY_ASSETS . 'admin/css/mapfy-editor.min.css', '', '', true);
            wp_register_style('mapfy-leaflet', MAPFY_ASSETS . 'public/css/leaflet.min.css');
            wp_register_script('mapfy-leaflet', MAPFY_ASSETS . 'public/js/leaflet.min.js', ['jquery'], '', true);
            wp_register_script('mapfy-leaflet-fullscreen', MAPFY_ASSETS . 'public/js/leaflet-fullscreen.min.js', ['jquery'], '', true);

            register_block_type(MAPFY_PATH . 'assets/blocks/mapfy', array('render_callback' => array($this, 'render_mapfy_block')));
        }

        public function render_mapfy_block($attributes)
        {
            $id = uniqid('mapfy_');
            $content = trim(preg_replace('/\s\s+/', ' ', $attributes['content']));
            $contentImage = !empty($attributes['imgUrl']) ? $attributes['imgUrl'] : '';
            $contentWidth = !empty($attributes['tooltipWidth']) ? $attributes['tooltipWidth'] : '';
            $customMarker = !empty($attributes['markerImg']['imgUrl']) ? $attributes['markerImg']['imgUrl'] : '';

            $classes = 'mapfy_leaflet_block';
            if (array_key_exists('align', $attributes)) {
                switch ($attributes['align']) {
                    case 'wide':
                        $classes .= ' alignwide';
                        break;
                    case 'full':
                        $classes .= ' alignfull';
                        break;
                }
            }

            ob_start();

            echo '<div id="' . esc_attr($id) . '" class="' . esc_attr($classes) . '" style="height:' . esc_attr($attributes['height']) . 'px"></div>';

?>

            <script>
                (function(e) {
                    // for Marker
                    function mapfySanitize(str) {
                        var text = document.createTextNode(str);
                        var p = document.createElement('p');
                        p.appendChild(text);
                        return p.innerHTML;
                    }

                    function is_loading() {
                        return document.body.classList.contains("loading");
                    }

                    function initilize() {

                        var mapfy = L.map("<?php echo esc_attr($id); ?>", {
                            fullscreenControl: true,
                            fullscreenControlOptions: {
                                position: 'topleft'
                                // forceSeparateButton: true, // force separate button to detach from zoom buttons, default false
                                // forcePseudoFullscreen: true, // force use of pseudo full screen even if full screen API is available, default false
                                // fullscreenElement: false
                            }
                        }).setView(["<?php echo esc_attr($attributes['lat']); ?>", "<?php echo esc_attr($attributes['lng']); ?>"], "<?php echo esc_attr($attributes['zoom']); ?>");

                        // for Marker
                        var markers = <?php echo wp_json_encode($attributes['markers']); ?>;
                        var center = [51.505, -0.09];

                        let defaultMarkerIcon = '<?php echo esc_url( MAPFY_IMAGES ) . 'marker-icon.png'; ?>'

                        mapfy.scrollWheelZoom.enable();

                        if (markers.length > 0) {
                            markers.forEach(function(marker) {
                                if (marker.content) {
                                    const content = mapfySanitize(marker.content)

                                    const customContent = marker?.tooltipImage?.imgUrl ? `
                                        <div>
                                            <img style="width: 100%" src='${marker?.tooltipImage?.imgUrl}' />
                                            ${content}
                                        </div>
                                    ` : content;

                                    const markerIcon = L.icon({
                                        iconUrl: marker?.markerImage?.imgUrl || defaultMarkerIcon,
                                        iconRetinaUrl: marker?.markerImage?.imgUrl || defaultMarkerIcon,
                                        iconSize: [25, 41],
                                        popupAnchor: [0, -18],
                                        // iconAnchor: [16, 37],
                                    });

                                    L.marker(marker.latlng, marker?.markerImage?.imgUrl ? {
                                            // draggable: true, // Make the icon dragable
                                            // title: "Hover Text", // Add a title
                                            // opacity: 0.5,
                                            icon: markerIcon // here assign the markerIcon var
                                        } : {})
                                        .bindPopup(customContent, marker?.tooltipImage?.imgUrl ? {
                                            minWidth: marker?.tooltipWidth,
                                            maxWidth: marker?.tooltipWidth
                                        } : {})
                                        .addTo(mapfy)
                                } else {
                                    const markerIcon = L.icon({
                                        iconUrl: marker?.markerImage?.imgUrl || defaultMarkerIcon,
                                        iconRetinaUrl: marker?.markerImage?.imgUrl || defaultMarkerIcon,
                                        iconSize: [25, 41],
                                        popupAnchor: [0, -18]
                                    });

                                    console.log("markerIcon", markerIcon)

                                    L.marker(marker.latlng, marker?.markerImage?.imgUrl ? {
                                        icon: markerIcon
                                    } : {}).addTo(mapfy)
                                }
                            })

                            const bounds = markers.map(function(marker) {
                                return marker.latlng
                            })

                            mapfy.fitBounds(bounds, {
                                padding: [50, 50]
                            })
                        }


                        // Map Attribution
                        L.tileLayer("<?php echo esc_attr($attributes['themeUrl']); ?>", {
                            attribution: '<?php echo wp_kses_post($attributes['themeAttribution']); ?>'
                        }).addTo(mapfy);

                        // Disable Scroll on Zoom
                        <?php if ($attributes['disableScrollZoom']) { ?>
                            mapfy.scrollWheelZoom.disable();
                        <?php } ?>

                        // Add Markers
                        <?php if (!empty($content)) { ?>
                            const content = "<?php echo esc_js($content); ?>";
                            const contentImage = "<?php echo esc_html($contentImage); ?>";
                            const contentWidth = "<?php echo esc_html($contentWidth); ?>";

                            <?php
                            $is_marker = !empty($customMarker) ? $customMarker : MAPFY_IMAGES . 'marker-icon.png';
                            ?>

                            const markerIcon = L.icon({
                                iconUrl: '<?php echo esc_url($is_marker); ?>',
                                iconRetinaUrl: '<?php echo esc_url($is_marker); ?>',
                                iconSize: [25, 41],
                                // iconAnchor: [16, 37],
                                popupAnchor: [0, -18]
                            });


                            const customContent = `
                                <div>
                                    <img style="width: 100%" src="${contentImage}" />
                                    ${content}
                                </div>
                            `
                            L.marker(["<?php echo esc_attr($attributes['lat']); ?>",
                                        "<?php echo esc_attr($attributes['lng']); ?>"
                                    ], {
                                        icon: markerIcon // here assign the markerIcon var
                                    } // Adjust the opacity
                                )
                                .addTo(mapfy)
                                .bindPopup(customContent, contentImage ? {
                                    minWidth: contentWidth,
                                    maxWidth: contentWidth
                                } : {})

                        <?php } else { ?>
                            L.marker(["<?php echo esc_attr($attributes['lat']); ?>", "<?php echo esc_attr($attributes['lng']); ?>"]).addTo(mapfy);
                        <?php } ?>

                        var timer = 100;

                        function checkRender() {
                            if (is_loading()) {
                                setTimeout(function() {
                                    checkRender();
                                }, timer);
                            } else {
                                mapfy.invalidateSize(true);
                            }
                        }

                        var container = document.getElementById("<?php echo esc_attr(esc_attr($id)); ?>");
                        var observer = ResizeObserver && new ResizeObserver(function() {
                            mapfy.invalidateSize(true);
                        });

                        observer && observer.observe(container);
                    }

                    document.addEventListener("DOMContentLoaded", initilize);
                })();
            </script>

<?php
            $output = ob_get_contents();
            ob_end_clean();

            return $output;
        }

        /**
         * Block Assets
         *
         * @return void
         */
        public function mapfy_enqueue_block_editor_assets()
        {
            // Blocks CSS
            wp_enqueue_style('mapfy-editor', MAPFY_ASSETS . 'admin/css/mapfy-editor.min.css');

            $data = [
                'images_url'                   => MAPFY_IMAGES
            ];

            wp_localize_script('mapfy-mapfy-editor-script', 'Mapfy', $data);
        }
    }
}