<?php
namespace Solid;

// Hook to add the admin menu page
add_action('admin_menu', function () {
    add_submenu_page(
        'solid-dynamics-settings',
        __('Custom CSS Usage', 'solid-dynamics'),   // Page title
        __('Custom CSS Usage', 'solid-dynamics'),            // Menu title
        'manage_options',          // Capability
        'solid-dynamics-custom-css-usage',  // Menu slug
        __NAMESPACE__ . '\render_custom_css_usage_page' // Callback function
    );
});

// Function to render the admin page
function render_custom_css_usage_page() {
    $results = get_all_elementor_custom_css();

    ?>
    <div class="wrap">
        <h1>Find Elementor Custom CSS Usage</h1>

        <?php

        if (empty($results)) {
            echo '<p>' . __('No posts contain custom css', 'solid-dynamics') . '.</p>';
            return;
        }

        echo '<h2>' . __('Results', 'solid-dynamics') . ':</h2>';
        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead><tr><th>ID</th><th>' . __('Title', 'solid-dynamics') . '</th><th>' . __('Post Type', 'solid-dynamics') . '</th><th>' . __('Status', 'solid-dynamics') . '</th><th>' . __('Widgets', 'solid-dynamics') . '</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $result) {
            $edit_link = get_edit_post_link($result['ID']);
            echo '<tr>';
            echo '<td>' . $result['ID'] . '</td>'; // Display the instance number
            echo '<td><a href="' . esc_url($edit_link) . '">' . esc_html($result['post_title']) . '</a></td>';
            echo '<td>' . esc_html($result['post_type']) . '</td>';
            echo '<td>' . esc_html($result['post_status']) . '</td>';
            echo '<td>' . esc_html(implode(', ', $result['widgets'])) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        ?>
    </div>
    <?php
}

// Function to get all Elementor widgets
function get_all_elementor_custom_css() {
    global $wpdb;

    $query = $wpdb->prepare(
        "SELECT ID, post_title, post_type, post_status, meta_value as elementor_data
         FROM $wpdb->postmeta
         JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->postmeta.post_id
         WHERE meta_key = '_elementor_data'
         AND meta_value LIKE '%custom_css%'
         AND post_status = 'publish'"
    );

    $posts = $wpdb->get_results($query, ARRAY_A);

    foreach ($posts as &$post) {
        $elementor_data = json_decode($post['elementor_data'], true);

        if (is_array($elementor_data)) {
            $post['widgets'] = collect_widgets_with_custom_css($elementor_data);
        }
    }

    return $posts;
}

function collect_widgets_with_custom_css($elementor_data, &$results = []) {

    foreach ($elementor_data as $widget) {
        if (isset($widget['settings']['custom_css'])) {
            $results[] = $widget['widgetType'] ?? $widget['elType'];
        }

        if (isset($widget['elements']) && is_array($widget['elements'])) {
            collect_widgets_with_custom_css($widget['elements'], $results);
        }
    }

    return $results;
}