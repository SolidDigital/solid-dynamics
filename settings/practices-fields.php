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
    $key = 'best_practices_caching_enabled';
    // Options are an array. Key are section id _ field id
    if (!$options[$key]) {
        $options[$key] = "1";
    } else {
        $options[$key] = "0";
    }
    return $options;
}

function test_minification($options) {
    return $options;
}

function test_performance_plugin_activation($options) {
    return $options;
}
