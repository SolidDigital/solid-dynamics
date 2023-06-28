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
    private $key_test_lazy_iframe = 'best_practices_lazy_iframe';

    private $key_logs = 'best_practices_analysis_logs';
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
                    'id'      => 'lazy_iframe',
                    'title'   => 'Lazy Load Iframes/Videos',
                    'desc'    => 'Is lazy loading enabled for iframes/videos?',
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
        $options[$this->key_test_lazy_iframe] = $this->no;
        $options[$this->key_logs] = "";

        $this->finalize($options);
    }

    public function solid_practices_run() {
        $options = get_option( $this->key_practices_option );

        $options = $this->test_caching($options);
        $options = $this->test_minification($options);
        $options = $this->test_performance_plugin_activation($options);
        $options = $this->test_js_defer($options);
        $options = $this->test_js_delay($options);
        $options = $this->test_lazy_image($options);
        $options = $this->test_lazy_iframe($options);

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

    public function test_minification($options) {
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
            $js_deferred = $perf_options['assets']['defer_js'] === "1";

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
            $js_delayed = $perf_options['assets']['delay_js'] === "1" && $perf_options['assets']['delay_js_behavior'] === 'all';

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
            $js_delayed = $perf_options['lazyload']['lazy_loading'] === "1";

            if ($js_delayed) {
                $options[$this->key_logs] .= "\nLazy images in perfmatters.";

                $options[$this->key_test_lazy_image] = $this->yes;
            }
        }

        return $options;
    }

    public function test_lazy_iframe($options) {
        $perf_options = get_option("perfmatters_options");

        if ($perf_options) {
            $lazy_iframe = $perf_options['lazyload']['lazy_loading_iframes'] === "1";

            if ($lazy_iframe) {
                $options[$this->key_logs] .= "\nLazy iframes in perfmatters.";

                $options[$this->key_test_lazy_iframe] = $this->yes;
            }
        }

        return $options;
    }
}

new PracticesFields();
