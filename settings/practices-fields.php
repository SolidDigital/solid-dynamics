<?php
namespace Solid;

class PracticesFields {

    private $yes = "1";
    private $no = "0";
    private $logging_enabled = true;

    private $key_practices_option = 'solid_practices_settings';
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

    private $key_logs = 'best_practices_analysis_logs';
    public $pagespeed_data = [];

    function __construct() {
        add_filter( 'wpsf_register_settings_solid_practices', array($this, 'wpsf_register_practices') );
        add_action( 'wpsf_before_settings_solid_practices', array($this, 'wpsf_analyze_button') );
        add_action( 'admin_action_solid_practices_run', array($this, 'solid_practices_run') );
        add_action( 'admin_action_solid_practices_clear', array($this, 'solid_practices_clear') );
    }

    public function wpsf_register_practices( $wpsf_settings ) {
        $wpsf_settings[] = array(
            'section_id'            => 'best_practices',
            'section_title'         => 'Best Practices',
            'section_order'         => 10,
            'fields'                => array(
                array(
                    'id'      => 'caching_enabled',
                    'title'   => 'Caching',
                    'desc'    => 'Is caching enabled?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'performance_plugin_activated',
                    'title'   => 'Performance',
                    'desc'    => 'Is a performance plugin activated?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'js_deferred',
                    'title'   => 'JS Defer',
                    'desc'    => 'Is JS deferred on most pages for most scripts?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'js_delayed',
                    'title'   => 'JS Delay',
                    'desc'    => 'Is JS delayed on most pages for most scripts?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'lazy_image',
                    'title'   => 'Lazy Load Images',
                    'desc'    => 'Is lazy loading enabled for below-the-fold images?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'lazy_bg',
                    'title'   => 'Lazy Load Background Images',
                    'desc'    => 'Is lazy loading enabled for background images?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'lazy_iframe',
                    'title'   => 'Lazy Load Iframes/Videos',
                    'desc'    => 'Is lazy loading enabled for iframes/videos?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'revisions_count',
                    'title'   => 'Revisions',
                    'desc'    => 'Is the total number of revisions in the DB below 100?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'draft_count',
                    'title'   => 'Drafts',
                    'desc'    => 'Is the total number of drafts in the DB below 100?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'trash_count',
                    'title'   => 'Trashed',
                    'desc'    => 'Is the total number of trashed posts in the DB below 100?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'comment_count',
                    'title'   => 'Comments',
                    'desc'    => 'Is the total number of spam/trashed comments below 100?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'orphan_meta',
                    'title'   => 'Orphan Meta',
                    'desc'    => 'Is the total number of orphan meta below 100?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'assets_minified',
                    'title'   => 'Assets',
                    'desc'    => 'Are frontend assets minfied?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'image_formats_modern',
                    'title'   => 'Images',
                    'desc'    => 'Are images served in modern formats (avif/webp)?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'image_files_optimized',
                    'title'   => 'Images',
                    'desc'    => 'Are image sizes optimized?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'image_size_attributes',
                    'title'   => 'Images',
                    'desc'    => 'Do images have width and height attributes?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'images_served_responsively',
                    'title'   => 'Images',
                    'desc'    => 'Are image sizes served responsively?',
                    'type'    => 'checkbox',
                    'default' => 0
                ),
                array(
                    'id'      => 'text_compression_enabled',
                    'title'   => 'Compression',
                    'desc'    => 'Is text compression enabled?',
                    'type'    => 'checkbox',
                    'default' => 0
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
        $options[$this->key_test_cache] = $this->no;
        $options[$this->key_test_performance] = $this->no;
        $options[$this->key_test_js_delay] = $this->no;
        $options[$this->key_test_js_defer] = $this->no;
        $options[$this->key_test_lazy_image] = $this->no;
        $options[$this->key_test_lazy_bg] = $this->no;
        $options[$this->key_test_lazy_iframe] = $this->no;
        $options[$this->key_test_revisions_count] = $this->no;
        $options[$this->key_test_draft_count] = $this->no;
        $options[$this->key_test_trash_count] = $this->no;
        $options[$this->key_test_comment_count] = $this->no;
        $options[$this->key_test_orphan_meta] = $this->no;

        $options[$this->key_logs] = "";

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
        $this->log('Starting caching test');

        // zero out test results to begin
        $options[$this->key_test_cache] = $this->no;

        // We're not testing SSL, we're testing caching
        stream_context_set_default( [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $url = site_url();
        $this->log("Testing URL: $url");
        $headers = get_headers($url);
        foreach ($headers as $key=>$value) {
            if ( stripos($value, "Cache") !== false ) {
                $this->log("Cache detected $key - $value");
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "cloudflare") !== false ) {
                $this->log("Cache potentially detected $key - $value");
                $options[$this->key_logs] .= "\nCache potentially detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "X-Forwarded-Proto:") !== false ) {
                $this->log("Cache detected $key - $value");
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "BigIp") !== false ) {
                $this->log("Cache detected $key - $value");
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "proxy") !== false ) {
                $this->log("Proxy detected $key - $value");
                $options[$this->key_logs] .= "\nProxy detected $key - $value";
                $options[$this->key_logs] .= "\nA Proxy may not mean cache is active, but a proxy can yield unexpected results that mimic caching symptoms.";
                $options[$this->key_test_cache] = $this->yes;
            } elseif (stripos($value, "varnish") !== false ) {
                $this->log("Cache detected $key - $value");
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif (stripos($value, "Vary: X-Forwarded-Proto") !== false ) {
                $this->log("Cache detected $key - $value");
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif (stripos($value, "P-LB") !== false ) {
                $this->log("Cache detected $key - $value");
                $options[$this->key_logs] .= "\nCache detected $key - $value";
                $options[$this->key_test_cache] = $this->yes;
            } elseif ( stripos($value, "Cache-Control") !== false ) {
                $this->log("Cache detected $key - $value");
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
                $this->log("Cache detected in $the_dir");
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

    public function get_pagespeed_test() {
        $test_subject_url = defined('TEST_SUBJECT_URL') ? TEST_SUBJECT_URL : site_url();
        $test_url = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$test_subject_url/";
        if(!empty($this->pagespeed_data)) {
            return $this->pagespeed_data;
        }

        error_log('    pagespeed data hasnt been gathered. calling it in, now.');
        try {
            $response = wp_remote_post( $test_url, [
                'method'      => 'GET',
                'timeout'     => 300,
                'httpversion' => '1.0',
                ],
            );

            $this->pagespeed_data = json_decode($response['body'],true);
            return $this->pagespeed_data;

        } catch (Exception $e) {
            echo "Error for $test_url - moving on\n";
            return false;
        }

    }

    public function test_minification($options) {

        error_log('Starting asset minification test');
        $test_key = 'best_practices_assets_minified';
        $log_key = 'best_practices_analysis_logs';

        // zero out test results to begin
        $options[$test_key] = "0";
        $pagespeed_results = $this->get_pagespeed_test();

        // error_log(print_r($pagespeed_results['lighthouseResult']['audits']['unminified-css']['details']['items'],true));
        // error_log(print_r($pagespeed_results['lighthouseResult']['audits']['unminified-javascript']['details']['items'],true));

        $css_unminified = !!count($pagespeed_results['lighthouseResult']['audits']['unminified-css']['details']['items']);
        $js_unminified = !!count($pagespeed_results['lighthouseResult']['audits']['unminified-javascript']['details']['items']);

        if ($css_unminified or $js_unminified) {
            $options[$test_key] = "0";
        } else {
            $options[$test_key] = "1";
        }
        return $options;
    }
    public function test_image_formats($options) {
        error_log('Starting image format test');
        $test_key = 'best_practices_image_formats_modern';
        $log_key = 'best_practices_analysis_logs';

        // zero out test results to begin
        $options[$test_key] = "0";
        $pagespeed_results = $this->get_pagespeed_test();

        // error_log(print_r($pagespeed_results['lighthouseResult']['audits']['modern-image-formats']['details']['items'],true));
        $old_image_formats = !!count($pagespeed_results['lighthouseResult']['audits']['modern-image-formats']['details']['items']);

        if ($old_image_formats) {
            $options[$test_key] = "0";
        } else {
            $options[$test_key] = "1";
        }

        return $options;
    }

    public function image_files_optimized($options) {
        error_log('Starting image optimization test');
        $test_key = 'best_practices_image_files_optimized';
        $log_key = 'best_practices_analysis_logs';

        // zero out test results to begin
        $options[$test_key] = "0";
        $pagespeed_results = $this->get_pagespeed_test();

        // error_log(print_r($pagespeed_results['lighthouseResult']['audits']['uses-optimized-images']['details']['items'],true));
        $sub_optimal_images = !!count($pagespeed_results['lighthouseResult']['audits']['uses-optimized-images']['details']['items']);

        if ($sub_optimal_images) {
            $options[$test_key] = "0";
        } else {
            $options[$test_key] = "1";
        }

        return $options;
    }

    public function test_image_size_attributes($options) {
        error_log('Starting image size attributes test');
        $test_key = 'best_practices_image_size_attributes';
        $log_key = 'best_practices_analysis_logs';

        // zero out test results to begin
        $options[$test_key] = "0";
        $pagespeed_results = $this->get_pagespeed_test();

        // error_log(print_r($pagespeed_results['lighthouseResult']['audits']['unsized-images']['details']['items'],true));
        $unsized_images = !!count($pagespeed_results['lighthouseResult']['audits']['unsized-images']['details']['items']);

        if ($unsized_images) {
            $options[$test_key] = "0";
        } else {
            $options[$test_key] = "1";
        }

        return $options;
    }

    public function test_images_served_responsively($options) {
        error_log('Starting image responsiveness test');
        $test_key = 'best_practices_images_served_responsively';
        $log_key = 'best_practices_analysis_logs';

        // zero out test results to begin
        $options[$test_key] = "0";
        $pagespeed_results = $this->get_pagespeed_test();

        // error_log(print_r($pagespeed_results['lighthouseResult']['audits']['uses-responsive-images']['details']['items'],true));
        $non_responsive_images = !!count($pagespeed_results['lighthouseResult']['audits']['uses-responsive-images']['details']['items']);

        if ($non_responsive_images) {
            $options[$test_key] = "0";
        } else {
            $options[$test_key] = "1";
        }

        return $options;
    }

    public function test_text_compression_enabled($options) {
        error_log('Starting text compression test');
        $test_key = 'best_practices_text_compression_enabled';
        $log_key = 'best_practices_analysis_logs';

        // zero out test results to begin
        $options[$test_key] = "0";
        $pagespeed_results = $this->get_pagespeed_test();

        // error_log(print_r($pagespeed_results['lighthouseResult']['audits']['uses-text-compression']['details']['items'],true));
        $uncompressed_text = !!count($pagespeed_results['lighthouseResult']['audits']['uses-text-compression']['details']['items']);

        if ($uncompressed_text) {
            $options[$test_key] = "0";
        } else {
            $options[$test_key] = "1";
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
            $js_deferred = array_dot_get($perf_options, 'assets.defer_js') === "1";

            if ($js_deferred) {
                $options[$this->key_logs] .= "\nJS deferred in perfmatters.";

                $options[$this->key_test_js_defer] = $this->yes;
            }
        }

        return $options;
    }

    public function test_js_delay($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $js_delayed = array_dot_get($perf_options, 'assets.delay_js') === "1" && array_dot_get($perf_options, 'assets.delay_js_behavior') === 'all';

            if ($js_delayed) {
                $options[$this->key_logs] .= "\nJS delayed in perfmatters.";

                $options[$this->key_test_js_delay] = $this->yes;
            }
        }

        return $options;
    }

    public function test_lazy_image($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $lazy_image = array_dot_get($perf_options, 'lazyload.lazy_loading') === "1";

            if ($lazy_image) {
                $options[$this->key_logs] .= "\nLazy images in perfmatters.";

                $options[$this->key_test_lazy_image] = $this->yes;
            }
        }

        return $options;
    }

    public function test_lazy_bg($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $lazy_bg = array_dot_get($perf_options, 'lazyload.css_background_images') === "1";

            if ($lazy_bg) {
                $options[$this->key_logs] .= "\nLazy background images in perfmatters.";

                $options[$this->key_test_lazy_bg] = $this->yes;
            }
        }

        return $options;
    }

    public function test_lazy_iframe($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $lazy_iframe = array_dot_get($perf_options, 'lazyload.lazy_loading_iframes') === "1";

            if ($lazy_iframe) {
                $options[$this->key_logs] .= "\nLazy iframes in perfmatters.";

                $options[$this->key_test_lazy_iframe] = $this->yes;
            }
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
        }

        return $options;
    }

    public function test_orphan_meta($options) {
        global $wpdb;

        $count = $wpdb->query("SELECT meta_id FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE p.ID IS NULL");

        $options[$this->key_logs] .= "\n" . $count . ' orphan meta.';

        if ($count < 100) {
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
