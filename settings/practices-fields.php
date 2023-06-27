<?php
namespace Solid;

add_filter( 'wpsf_register_settings_solid_practices', 'Solid\wpsf_register_practices' );
add_action( 'wpsf_before_settings_solid_practices', 'Solid\wpsf_analyze_button');
add_action( 'admin_action_solid_practices_run', 'Solid\solid_practices_run');

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
EOT;
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
            error_log("Cache detected $key - $value");
            $options[$log_key] .= "\nCache detected $key - $value";
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

    return $options;
}

function test_minification($options) {
    $url = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://disturbedone.forumotion.com/';

    error_log('Starting css minification test');
    $test_key = 'best_practices_css_minified';
    $log_key = 'best_practices_analysis_logs';

    // zero out test results to begin
    $options[$test_key] = "0";


    try {
        $response = wp_remote_post( $url, [
            'method'      => 'GET',
            'timeout'     => 300,
            'httpversion' => '1.0',
            ],
        );
        $results = json_decode($response['body'],true);
        error_log(print_r($results['lighthouseResult']['audits']['unminified-css']['details']['items'],true));

    } catch (Exception $e) {
        echo "Error for $url - moving on\n";
        return $options;
    }

    if (count($results['lighthouseResult']['audits']['unminified-css']['details']['items']) > 0) {
        $options[$test_key] = "1"; 
    } else {
        $options[$test_key] = "0"; 

    }

    return $options;
}

function test_performance_plugin_activation($options) {
    return $options;
}


