<?php
namespace Solid;

class PracticesFields {

    private $yes = 'yes';
    private $no = 'no';
    private $empty = 'empty';
    private $error = 'error';
    private $checked = '1';
    private $unchecked = '0';
    private $logging_enabled = true;

    private $key_practices_option = 'solid_practices_settings';
    private $key_test_test_url = 'best_practices_test_url';
    private $key_test_skip_pagespeed = 'best_practices_skip_pagespeed';
    private $key_test_cache = 'best_practices_caching_enabled';
    private $key_test_performance = 'best_practices_performance_plugin_activated';
    private $key_test_js_delay = 'best_practices_js_delayed';
    private $key_test_js_defer = 'best_practices_js_deferred';
    private $key_test_lazy_image = 'best_practices_lazy_image';
    private $key_test_lazy_bg = 'best_practices_lazy_bg';
    private $key_test_lazy_iframe = 'best_practices_lazy_iframe';
    private $key_test_revisions_count = 'best_practices_revisions_count';
    private $key_test_draft_count = 'best_practices_draft_count';
    private $key_test_trash_count = 'best_practices_trash_count';
    private $key_test_comment_count = 'best_practices_comment_count';
    private $key_test_orphan_meta = 'best_practices_orphan_meta';
    private $key_test_image_files_optimized = 'best_practices_image_files_optimized';
    private $key_test_assets_minified = 'best_practices_assets_minified';
    private $key_test_image_formats_modern = 'best_practices_image_formats_modern';
    private $key_test_image_size_attributes = 'best_practices_image_size_attributes';
    private $key_test_images_served_responsively = 'best_practices_images_served_responsively';
    private $key_test_text_compression_enabled = 'best_practices_text_compression_enabled';

    private $key_logs = 'best_practices_analysis_logs';
    public $pagespeed_data = null;

    function __construct() {
        add_filter( 'wpsf_register_settings_solid_nonautomated_practices', array($this, 'wpsf_register_nonautomated_practices') );
        add_filter( 'wpsf_register_settings_solid_practices', array($this, 'wpsf_register_practices') );
        add_action( 'wpsf_before_settings_solid_practices', array($this, 'wpsf_analyze_button') );
        add_action( 'admin_action_solid_practices_run', array($this, 'solid_practices_run') );
        add_action( 'admin_action_solid_practices_clear', array($this, 'solid_practices_clear') );
    }

    public function wpsf_register_nonautomated_practices() {
        $practices = array_map('str_getcsv', file(__DIR__ . '/practices.csv'));
        $fields = [];
        foreach($practices as $practice) {
            
            $interrogative = strtolower(explode(" ", $practice[0])[0]);
            if (in_array($interrogative,  ['is','does','are'])) {

                $field = [
                    'id' => str_replace('-', '_', sanitize_title($practice[0])),
                    'title' => $practice[0],
                    'desc' => $practice[7],
                    'type' => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty,
                ];
                $fields[] = $field;
            }
        }
        $wpsf_settings[] = [
            'section_id'            => 'best_practices_nonautomated',
            'section_title'         => 'Best Practices (non-automated)',
            'section_order'         => 11,
            'fields'                => $fields,
        ];

        // error_log(print_r($fields,true));
        error_log('number of questions: ' . count($fields));
        return $fields;
    }

    public function wpsf_register_practices( $wpsf_settings ) {
        $wpsf_settings[] = array(
            'section_id'            => 'best_practices',
            'section_title'         => 'Best Practices',
            'section_order'         => 10,
            'fields'                => array(
                array(
                    'id'      => 'test_url',
                    'title'   => 'Test URL',
                    'desc'    => 'Fill this out to override using the homepage to test',
                    'type'    => 'text',
                    'default' => ''
                ),
                array(
                    'id'      => 'skip_pagespeed',
                    'title'   => 'Pagespeed',
                    'desc'    => 'Skip running the Google Pagespeed Insights Test',
                    'type'    => 'checkbox',
                    'default' => $this->unchecked
                ),
                array(
                    'id'      => 'caching_enabled',
                    'title'   => 'Caching',
                    'desc'    => 'Is caching enabled?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'performance_plugin_activated',
                    'title'   => 'Performance',
                    'desc'    => 'Is a performance plugin activated?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'js_deferred',
                    'title'   => 'JS Defer',
                    'desc'    => 'Is JS deferred on most pages for most scripts?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'js_delayed',
                    'title'   => 'JS Delay',
                    'desc'    => 'Is JS delayed on most pages for most scripts?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'lazy_image',
                    'title'   => 'Lazy Load Images',
                    'desc'    => 'Is lazy loading enabled for below-the-fold images?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'lazy_bg',
                    'title'   => 'Lazy Load Background Images',
                    'desc'    => 'Is lazy loading enabled for background images?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'lazy_iframe',
                    'title'   => 'Lazy Load Iframes/Videos',
                    'desc'    => 'Is lazy loading enabled for iframes/videos?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'revisions_count',
                    'title'   => 'Revisions',
                    'desc'    => 'Is the total number of revisions in the DB below 100?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'draft_count',
                    'title'   => 'Drafts',
                    'desc'    => 'Is the total number of drafts in the DB below 100?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'trash_count',
                    'title'   => 'Trashed',
                    'desc'    => 'Is the total number of trashed posts in the DB below 100?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'comment_count',
                    'title'   => 'Comments',
                    'desc'    => 'Is the total number of spam/trashed comments below 100?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'orphan_meta',
                    'title'   => 'Orphan Meta',
                    'desc'    => 'Is the total number of orphan meta below 100?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'assets_minified',
                    'title'   => 'Assets',
                    'desc'    => 'Are frontend assets minfied?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'image_formats_modern',
                    'title'   => 'Images',
                    'desc'    => 'Are images served in modern formats (avif/webp)?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'image_files_optimized',
                    'title'   => 'Images',
                    'desc'    => 'Are image sizes optimized?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'image_size_attributes',
                    'title'   => 'Images',
                    'desc'    => 'Do images have width and height attributes?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'images_served_responsively',
                    'title'   => 'Images',
                    'desc'    => 'Are image sizes served responsively?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'text_compression_enabled',
                    'title'   => 'Compression',
                    'desc'    => 'Is text compression enabled?',
                    'type'    => 'select',
                    'choices' => $this->getYesNoErrorChoices(),
                    'default' => $this->empty
                ),
                array(
                    'id'      => 'analysis_logs',
                    'title'   => 'Analysis Logs',
                    'desc'    => 'Logs from the last run analysis',
                    'type'    => 'textarea',
                    'default' => ''
                )
            ),
        );
        return $wpsf_settings;
    }

    private function getYesNoErrorChoices() {
        return array(
            $this->empty => 'Not yet run',
            $this->yes => 'Yes',
            $this->no => 'No',
            $this->error => 'Error'
        );
    }

    public function wpsf_analyze_button() {
        $admin_url = admin_url( 'admin.php' );
        echo <<<EOT
<form method="POST" action="$admin_url">
    <input type="hidden" name="action" value="solid_practices_run" />
    <p><input type="submit" class="button button-primary" value="Run Automated Analysis" /></p>
</form>
<form method="POST" action="$admin_url">
    <input type="hidden" name="action" value="solid_practices_clear" />
    <p><input type="submit" class="button button-primary" value="Clear Results" /></p>
</form>
EOT;
    }

    public function solid_practices_clear() {
        $options = get_option($this->key_practices_option);
        $options[$this->key_test_cache] = $this->empty;
        $options[$this->key_test_performance] = $this->empty;
        $options[$this->key_test_js_delay] = $this->empty;
        $options[$this->key_test_js_defer] = $this->empty;
        $options[$this->key_test_lazy_image] = $this->empty;
        $options[$this->key_test_lazy_bg] = $this->empty;
        $options[$this->key_test_lazy_iframe] = $this->empty;
        $options[$this->key_test_revisions_count] = $this->empty;
        $options[$this->key_test_draft_count] = $this->empty;
        $options[$this->key_test_trash_count] = $this->empty;
        $options[$this->key_test_comment_count] = $this->empty;
        $options[$this->key_test_orphan_meta] = $this->empty;
        $options[$this->key_test_assets_minified] = $this->empty;
        $options[$this->key_test_image_formats_modern] = $this->empty;
        $options[$this->key_test_image_files_optimized] = $this->empty;
        $options[$this->key_test_image_size_attributes] = $this->empty;
        $options[$this->key_test_images_served_responsively] = $this->empty;
        $options[$this->key_test_text_compression_enabled] = $this->empty;

        $options[$this->key_logs] = '';

        $this->finalize($options);
    }

    public function solid_practices_run() {
        $options = get_option( $this->key_practices_option );

        $options = $this->test_caching($options);
        $options = $this->test_minification($options);
        $options = $this->test_image_formats($options);
        $options = $this->image_files_optimized($options);
        $options = $this->test_image_size_attributes($options);
        $options = $this->test_images_served_responsively($options);
        $options = $this->test_text_compression_enabled($options);


        $options = $this->test_performance_plugin_activation($options);
        $options = $this->test_js_defer($options);
        $options = $this->test_js_delay($options);
        $options = $this->test_lazy_image($options);
        $options = $this->test_lazy_bg($options);
        $options = $this->test_lazy_iframe($options);
        $options = $this->test_revision_count($options);
        $options = $this->test_draft_count($options);
        $options = $this->test_trash_count($options);
        $options = $this->test_comment_count($options);
        $options = $this->test_orphan_meta($options);

        $this->finalize($options);
    }

    private function finalize($options) {
        update_option($this->key_practices_option, $options);
        wp_redirect(home_url() . '/wp-admin/admin.php?page=solid-practices-settings');
        exit;
    }

    public function test_caching($options) {
        // The code for this test courtesy of https://wordpress.org/plugins/detect-cache/
        // zero out test results to begin
        $options[$this->key_test_cache] = $this->no;

        // We're not testing SSL, we're testing caching
        stream_context_set_default( [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $url = $this->get_test_url($options);
        $this->log("Testing URL: $url");
        $headers = get_headers($url);
        foreach ($headers as $key=>$value) {
            if ( stripos($value, "Cache") !== false ) {
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "cloudflare") !== false ) {
                $options[$this->key_logs] .= "\nCache potentially detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "X-Forwarded-Proto:") !== false ) {
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "BigIp") !== false ) {
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "proxy") !== false ) {
                $options[$this->key_logs] .= "\nProxy detected $key - $value";
                $options[$this->key_logs] .= "\nA Proxy may not mean cache is active, but a proxy can yield unexpected results that mimic caching symptoms.";
                $options[$this->key_test_cache] = $this->yes;
            } elseif (stripos($value, "varnish") !== false ) {
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif (stripos($value, "Vary: X-Forwarded-Proto") !== false ) {
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif (stripos($value, "P-LB") !== false ) {
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "Cache-Control") !== false ) {
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            }
        }

        $path = get_home_path();
        $targetdir = "wp-content";
        $dir = "$path$targetdir";
        $potential_cache_files = scandir($dir);
        foreach ($potential_cache_files as $the_dir) {
            if (stripos($the_dir, 'cache') !== false) {
                $options[$this->key_logs] .= "\nCache detected in $the_dir";
                $options[$this->key_test_cache] = $this->yes;
            }
        }

        return $options;
    }

    private function log($msg) {
        if ($this->logging_enabled) {
            error_log($msg);
        }
    }

    private function get_pagespeed_test($options) {
        if(!empty($this->pagespeed_data) || $options[$this->key_test_skip_pagespeed] === $this->checked) {
            return $this->pagespeed_data;
        }

        $test_url = $this->get_test_url($options);
        $test_url = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$test_url/";

        try {
            $response = wp_remote_post( $test_url, [
                'method'      => 'GET',
                'timeout'     => 300,
                'httpversion' => '1.0',
                ],
            );

            $this->pagespeed_data = json_decode($response['body'],true);
            if (array_key_exists('error', $this->pagespeed_data)) {
                $this->log('Error with pagespeed report.');
                $this->log(print_r($this->pagespeed_data, true));
                $this->pagespeed_data = null;
            }
            return $this->pagespeed_data;

        } catch (\Exception $e) {
            echo "Error for $test_url - moving on\n";
            return false;
        }

    }

    private function get_test_url($options) {
        return array_dot_get($options, $this->key_test_test_url) ?: site_url();
    }
    public function test_minification($options) {

        $pagespeed_results = $this->get_pagespeed_test($options);
        if (!$pagespeed_results) {
            if ($options[$this->key_test_skip_pagespeed] !== $this->checked) {
                $options[$this->key_test_assets_minified] = $this->error;
            }
            return $options;
        }

        $css_unminified = !!count(array_dot_get($pagespeed_results, 'lighthouseResult.audits.unminified-css.details.items'));
        $js_unminified = !!count(array_dot_get($pagespeed_results, 'lighthouseResult.audits.unminified-javascript.details.items'));

        if ($css_unminified or $js_unminified) {
            $options[$this->key_test_assets_minified] = $this->no;
        } else {
            $options[$this->key_test_assets_minified] = $this->yes;
        }
        return $options;
    }
    public function test_image_formats($options) {

        $pagespeed_results = $this->get_pagespeed_test($options);
        if (!$pagespeed_results) {
            if ($options[$this->key_test_skip_pagespeed] !== $this->checked) {
                $options[ $this->key_test_image_formats_modern ] = $this->error;
            }
            return $options;
        }

        $old_image_formats = !!count(array_dot_get($pagespeed_results, 'lighthouseResult.audits.modern-image-formats.details.items'));

        if ($old_image_formats) {
            $options[$this->key_test_image_formats_modern] = $this->no;
        } else {
            $options[$this->key_test_image_formats_modern] = $this->yes;
        }

        return $options;
    }

    public function image_files_optimized($options) {

        $pagespeed_results = $this->get_pagespeed_test($options);
        if (!$pagespeed_results) {
            if ($options[$this->key_test_skip_pagespeed] !== $this->checked) {
                $options[ $this->key_test_image_files_optimized ] = $this->error;
            }
            return $options;
        }

        $sub_optimal_images = !!count(array_dot_get($pagespeed_results, 'lighthouseResult.audits.uses-optimized-images.details.items'));

        if ($sub_optimal_images) {
            $options[$this->key_test_image_files_optimized] = $this->no;
        } else {
            $options[$this->key_test_image_files_optimized] = $this->yes;
        }

        return $options;
    }

    public function test_image_size_attributes($options) {
        $pagespeed_results = $this->get_pagespeed_test($options);
        if (!$pagespeed_results) {
            if ($options[$this->key_test_skip_pagespeed] !== $this->checked) {
                $options[ $this->key_test_image_size_attributes ] = $this->error;
            }
            return $options;
        }

        $unsized_images = !!count(array_dot_get($pagespeed_results, 'lighthouseResult.audits.unsized-images.details.items'));

        if ($unsized_images) {
            $options[$this->key_test_image_size_attributes] = $this->no;
        } else {
            $options[$this->key_test_image_size_attributes] = $this->yes;
        }

        return $options;
    }

    public function test_images_served_responsively($options) {

        $pagespeed_results = $this->get_pagespeed_test($options);
        if (!$pagespeed_results) {
            if ($options[$this->key_test_skip_pagespeed] !== $this->checked) {
                $options[ $this->key_test_images_served_responsively ] = $this->error;
            }
            return $options;
        }

        $non_responsive_images = !!count(array_dot_get($pagespeed_results, 'lighthouseResult.audits.uses-responsive-images.details.items'));

        if ($non_responsive_images) {
            $options[$this->key_test_images_served_responsively] = $this->no;
        } else {
            $options[$this->key_test_images_served_responsively] = $this->yes;
        }

        return $options;
    }

    public function test_text_compression_enabled($options) {

        $pagespeed_results = $this->get_pagespeed_test($options);
        if (!$pagespeed_results) {
            if ($options[$this->key_test_skip_pagespeed] !== $this->checked) {
                $options[ $this->key_test_text_compression_enabled ] = $this->error;
            }
            return $options;
        }


        $uncompressed_text = !!count(array_dot_get($pagespeed_results, 'lighthouseResult.audits.uses-text-compression.details.items'));

        if ($uncompressed_text) {
            $options[$this->key_test_text_compression_enabled] = $this->no;
        } else {
            $options[$this->key_test_text_compression_enabled] = $this->yes;
        }

        return $options;
    }

    public function test_performance_plugin_activation($options) {
        $active_plugins = get_option('active_plugins');

        // Many caching plugins have minification and other performance functionality included - those are added below
        $performance_plugins = array(
            'hummingbird-performance',
            'nitropack',
            'perfmatters',
            'performance-lab',
            'sg-cachepress',
            'tenweb-speed-optimizer',
            'w3-total-cache',
            'wp-fastest-cache',
            'wp-optimize',
            'wp-rocket',
            'wp-super-minify'
        );
        foreach ($active_plugins as $plugin) {
            $plugin = explode('/', $plugin)[0];
            if (in_array($plugin, $performance_plugins)) {
                $options[$this->key_test_performance] = $this->yes;
                $options[$this->key_logs] .= "\nPerformance plugin found: $plugin";
            }
        }
        return $options;
    }

    public function test_js_defer($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $js_deferred = array_dot_get($perf_options, 'assets.defer_js') === $this->yes;

            if ($js_deferred) {
                $options[$this->key_logs] .= "\nJS deferred in perfmatters.";

                $options[$this->key_test_js_defer] = $this->yes;
            } else {
                $options[$this->key_test_js_defer] = $this->no;
            }
        } else {
            $options[$this->key_test_js_defer] = $this->error;
        }

        return $options;
    }

    public function test_js_delay($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $js_delayed = array_dot_get($perf_options, 'assets.delay_js') === $this->yes && array_dot_get($perf_options, 'assets.delay_js_behavior') === 'all';

            if ($js_delayed) {
                $options[$this->key_logs] .= "\nJS delayed in perfmatters.";

                $options[$this->key_test_js_delay] = $this->yes;
            } else {
                $options[$this->key_test_js_delay] = $this->no;
            }
        } else {
            $options[$this->key_test_js_delay] = $this->error;
        }

        return $options;
    }

    public function test_lazy_image($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $lazy_image = array_dot_get($perf_options, 'lazyload.lazy_loading') === $this->yes;

            if ($lazy_image) {
                $options[$this->key_logs] .= "\nLazy images in perfmatters.";

                $options[$this->key_test_lazy_image] = $this->yes;
            } else {
                $options[$this->key_test_lazy_image] = $this->no;
            }
        } else {
            $options[$this->key_test_lazy_image] = $this->error;
        }

        return $options;
    }

    public function test_lazy_bg($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $lazy_bg = array_dot_get($perf_options, 'lazyload.css_background_images') === $this->yes;

            if ($lazy_bg) {
                $options[$this->key_logs] .= "\nLazy background images in perfmatters.";

                $options[$this->key_test_lazy_bg] = $this->yes;
            } else {
                $options[$this->key_test_lazy_bg] = $this->no;
            }
        } else {
            $options[$this->key_test_lazy_bg] = $this->error;
        }

        return $options;
    }

    public function test_lazy_iframe($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $lazy_iframe = array_dot_get($perf_options, 'lazyload.lazy_loading_iframes') === $this->yes;

            if ($lazy_iframe) {
                $options[$this->key_logs] .= "\nLazy iframes in perfmatters.";

                $options[$this->key_test_lazy_iframe] = $this->yes;
            } else {
                $options[$this->key_test_lazy_iframe] = $this->no;
            }
        } else {
            $options[$this->key_test_lazy_iframe] = $this->error;
        }

        return $options;
    }

    public function test_revision_count($options) {
        $posts = get_posts([
            'post_type' => 'revision',
            'post_status' => 'any',
            'numberposts' => -1,
            'fields' => 'ids'
        ]);

        $options[$this->key_logs] .= "\n" . count($posts) . ' revisions.';

        if (count($posts) < 100) {
            $options[$this->key_test_revisions_count] = $this->yes;
        } else {
            $options[$this->key_test_revisions_count] = $this->no;
        }

        return $options;
    }

    public function test_draft_count($options) {
        $posts = get_posts([
            'post_type' => 'any',
            'post_status' => 'draft',
            'numberposts' => -1,
            'fields' => 'ids'
        ]);

        $options[$this->key_logs] .= "\n" . count($posts) . ' draft posts.';

        if (count($posts) < 100) {
            $options[$this->key_test_draft_count] = $this->yes;
        } else {
            $options[$this->key_test_draft_count] = $this->no;
        }

        return $options;
    }

    public function test_trash_count($options) {
        $posts = get_posts([
            'post_type' => 'any',
            'post_status' => 'trash',
            'numberposts' => -1,
            'fields' => 'ids'
        ]);

        $options[$this->key_logs] .= "\n" . count($posts) . ' trashed posts.';

        if (count($posts) < 100) {
            $options[$this->key_test_trash_count] = $this->yes;
        } else {
            $options[$this->key_test_trash_count] = $this->no;
        }

        return $options;
    }

    public function test_comment_count($options) {
        $comments = get_comments([
            'count' => true,
            'status' => ['trash', 'spam']
        ]);

        $options[$this->key_logs] .= "\n" . $comments . ' spam/trashed comments.';

        if ($comments < 100) {
            $options[$this->key_test_comment_count] = $this->yes;
        } else {
            $options[$this->key_test_comment_count] = $this->yes;
        }

        return $options;
    }

    public function test_orphan_meta($options) {
        global $wpdb;

        $count = $wpdb->query("SELECT meta_id FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE p.ID IS NULL");

        $options[$this->key_logs] .= "\n" . $count . ' orphan meta.';

        if ($count < 100) {
            $options[$this->key_test_orphan_meta] = $this->yes;
        } else {
            $options[$this->key_test_orphan_meta] = $this->yes;
        }

        return $options;
    }
}

new PracticesFields();

// Helpers
function array_dot_get($array, $path, $default = null) {
	$value = $array;
	$parts = explode('.', $path);

	foreach($parts as $part) {
		if (isset($value[$part])) {
			$value = $value[$part];
		} else {
			$value = $default;
			break;
		}
	}

	return $value;
}
