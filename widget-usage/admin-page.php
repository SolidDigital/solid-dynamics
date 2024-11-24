<?php
namespace Solid;

// Hook to add the admin menu page
add_action('admin_menu', function () {
    add_menu_page(
        'Elementor Widget Usage',   // Page title
        'Widget Usage',            // Menu title
        'manage_options',          // Capability
        'elementor-widget-usage',  // Menu slug
        __NAMESPACE__ . '\render_widget_usage_page' // Callback function
    );
});

// Function to render the admin page
function render_widget_usage_page() {
    $widgets = get_all_elementor_widgets();

    ?>
    <div class="wrap">
        <h1>Find Elementor Widget Usage</h1>
        <form method="post" id="widget-usage-form">
            <label for="widget_name">Select Widget:</label>
            <select id="widget_name" name="widget_name" required>
                <option value="">-- Select a Widget --</option>
                <?php foreach ($widgets as $widget) : ?>
                    <option value="<?php echo esc_attr($widget); ?>">
                        <?php echo esc_html($widget); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php submit_button('Find Usage'); ?>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['widget_name'])) {
            $widget_name = sanitize_text_field($_POST['widget_name']);
            $results = find_widget_usage($widget_name);

            if (!empty($results)) {
                // Order results by widget count (highest to lowest)
                usort($results, function ($a, $b) {
                    return $b->widget_count <=> $a->widget_count;
                });

                $total_instances = array_sum(array_column($results, 'widget_count'));

                echo '<h2>Results:</h2>';
                echo '<p>Total Instances Across All Posts: ' . $total_instances . '</p>';
                echo '<table class="widefat fixed" cellspacing="0">';
                echo '<thead><tr><th>#</th><th>Title</th><th>Post Type</th><th>Status</th><th>Count</th></tr></thead>';
                echo '<tbody>';

                $count = 1; // Initialize instance counter
                foreach ($results as $result) {
                    $edit_link = get_edit_post_link($result->ID);
                    echo '<tr>';
                    echo '<td>' . $count . '</td>'; // Display the instance number
                    echo '<td><a href="' . esc_url($edit_link) . '">' . esc_html($result->post_title) . '</a></td>';
                    echo '<td>' . esc_html($result->post_type) . '</td>';
                    echo '<td>' . esc_html($result->post_status) . '</td>';
                    echo '<td>' . esc_html($result->widget_count) . '</td>';
                    echo '</tr>';
                    $count++;
                }

                echo '</tbody></table>';
            } else {
                echo '<p>No templates or posts contain this widget.</p>';
            }
        }
        ?>
    </div>
    <?php
}

// Function to get all Elementor widgets
function get_all_elementor_widgets() {
    global $wpdb;

    // Fetch all `_elementor_data` values
    $query = $wpdb->prepare(
        "SELECT meta_value
         FROM $wpdb->postmeta
         WHERE meta_key = %s",
        '_elementor_data'
    );

    $results = $wpdb->get_col($query);

    $widgets = [];

    foreach ($results as $meta_value) {
        $data = json_decode($meta_value, true);

        if (is_array($data)) {
            collect_widgets_from_data($data, $widgets);
        }
    }

    // Return unique widgets, sorted alphabetically
    $widgets = array_unique($widgets);
    sort($widgets);

    return $widgets;
}

// Helper function to recursively collect widget types
function collect_widgets_from_data($data, &$widgets) {
    foreach ($data as $element) {
        if (isset($element['elType']) && $element['elType'] === 'widget' && isset($element['widgetType'])) {
            $widgets[] = $element['widgetType'];
        }

        if (isset($element['elements']) && is_array($element['elements'])) {
            collect_widgets_from_data($element['elements'], $widgets);
        }
    }
}

// Function to find widget usage
function find_widget_usage($widget_name) {
    global $wpdb;

    // Query for all Elementor post meta
    $query = $wpdb->prepare(
        "SELECT post_id, meta_value
         FROM $wpdb->postmeta
         WHERE meta_key = %s",
        '_elementor_data'
    );

    $posts = $wpdb->get_results($query);
    $results = [];

    foreach ($posts as $post) {
        $data = json_decode($post->meta_value, true);

        if (is_array($data)) {
            $widget_count = count_widgets_in_data($data, $widget_name);
            if ($widget_count > 0) {
                $post_data = get_post($post->post_id);
                $results[] = (object) [
                    'ID' => $post_data->ID,
                    'post_title' => $post_data->post_title,
                    'post_type' => $post_data->post_type,
                    'post_status' => $post_data->post_status,
                    'widget_count' => $widget_count,
                ];
            }
        }
    }

    return $results;
}

// Helper function to count widget instances in nested data
function count_widgets_in_data($data, $widget_name) {
    $count = 0;

    foreach ($data as $element) {
        // Check if the element is a widget and matches the desired widget name
        if (
            isset($element['elType']) &&
            $element['elType'] === 'widget' &&
            isset($element['widgetType']) &&
            $element['widgetType'] === $widget_name
        ) {
            $count++;
        }

        // Recursively check nested elements
        if (isset($element['elements']) && is_array($element['elements'])) {
            $count += count_widgets_in_data($element['elements'], $widget_name);
        }
    }

    return $count;
}
