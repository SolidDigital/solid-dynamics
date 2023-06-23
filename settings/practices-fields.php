<?php
namespace Solid;

add_filter( 'wpsf_register_settings_solid_practices', 'Solid\wpsf_register_practices' );
add_action( 'wpsf_before_settings_solid_practices', 'Solid\wpsf_analyze_button');
add_action( 'admin_action_solid_practices_run', 'Solid\solid_practices_run');
add_action( 'admin_action_solid_practices_clear', 'Solid\solid_practices_clear');


function wpsf_register_practices( $wpsf_settings ) {
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

function wpsf_analyze_button() {
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

function solid_practices_clear() {
    $practices_option_key = 'solid_practices_settings';
    $options = get_option( $practices_option_key );
    $cache_key = 'best_practices_caching_enabled';
    $perf_key = 'best_practices_performance_plugin_activated';
    $log_key = 'best_practices_analysis_logs';

    $options[$cache_key] = "0";
    $options[$perf_key] = "0";
    $options[$log_key] = "";

    update_option($practices_option_key, $options);
    wp_redirect(home_url() . '/wp-admin/admin.php?page=solid-practices-settings');
    exit;
}

function solid_practices_run() {
    $practices_option_key = 'solid_practices_settings';
    $options = get_option( $practices_option_key );

    $options = test_caching($options);
    $options = test_minification($options);
    $options = test_performance_plugin_activation($options);

    update_option($practices_option_key, $options);
    wp_redirect(home_url() . '/wp-admin/admin.php?page=solid-practices-settings');
    exit;
}

function test_caching($options) {
    // The code for this test courtesy of https://wordpress.org/plugins/detect-cache/
    error_log('Starting caching test');
    $test_key = 'best_practices_caching_enabled';
    $log_key = 'best_practices_analysis_logs';

    // zero out test results to begin
    $options[$test_key] = "0";

    // We're not testing SSL, we're testing caching
    stream_context_set_default( [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]);
    $url = site_url();
    error_log("Testing URL: $url");
    $headers = get_headers($url);
    foreach ($headers as $key=>$value) {
        if ( stripos($value, "Cache") !== false ) {
            error_log("Cache detected $key - $value");
            $options[$log_key] .= "\nCache detected $key - $value";
            $options[$test_key] = "1";
        } elseif ( stripos($value, "cloudflare") !== false ) {
            error_log("Cache potentially detected $key - $value");
            $options[$log_key] .= "\nCache potentially detected $key - $value";
            $options[$test_key] = "1";
        } elseif ( stripos($value, "X-Forwarded-Proto:") !== false ) {
            error_log("Cache detected $key - $value");
            $options[$log_key] .= "\nCache detected $key - $value";
            $options[$test_key] = "1";
        } elseif ( stripos($value, "BigIp") !== false ) {
            error_log("Cache detected $key - $value");
            $options[$log_key] .= "\nCache detected $key - $value";
            $options[$test_key] = "1";
        } elseif ( stripos($value, "proxy") !== false ) {
            error_log("Proxy detected $key - $value");
            $options[$log_key] .= "\nProxy detected $key - $value";
            $options[$log_key] .= "\nA Proxy may not mean cache is active, but a proxy can yield unexpected results that mimic caching symptoms.";
            $options[$test_key] = "1";
        } elseif (stripos($value, "varnish") !== false ) {
            error_log("Cache detected $key - $value");
            $options[$log_key] .= "\nCache detected $key - $value";
            $options[$test_key] = "1";
        } elseif (stripos($value, "Vary: X-Forwarded-Proto") !== false ) {
            error_log("Cache detected $key - $value");
            $options[$log_key] .= "\nCache detected $key - $value";
            $options[$test_key] = "1";
        } elseif (stripos($value, "P-LB") !== false ) {
            error_log("Cache detected $key - $value");
            $options[$log_key] .= "\nCache detected $key - $value";
            $options[$test_key] = "1";
        } elseif ( stripos($value, "Cache-Control") !== false ) {
            error_log("Cache detected $key - $value");
            $options[$log_key] .= "\nCache detected $key - $value";
            $options[$test_key] = "1";
        }
    }

    $path = get_home_path();
    $targetdir = "wp-content";
    $dir = "$path$targetdir";
    $potential_cache_files = scandir($dir);
    foreach ($potential_cache_files as $the_dir) {
        if (stripos($the_dir, 'cache') !== false) {
            error_log("Cache detected in $the_dir");
            $options[$log_key] .= "\nCache detected in $the_dir";
            $options[$test_key] = "1";
        }
    }

    return $options;
}

function test_minification($options) {
    return $options;
}

function test_performance_plugin_activation($options) {
    $test_key = 'best_practices_performance_plugin_activated';
    $log_key = 'best_practices_analysis_logs';

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
            $options[$test_key] = "1";
            $options[$log_key] .= "\nPerformance plugin found: $plugin";
        }
    }
    return $options;
}
