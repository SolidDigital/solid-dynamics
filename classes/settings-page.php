<?php
namespace Solid;

const SETTINGS_PAGE_KEY = 'solid_dynamics';
const SETTINGS_OPTION_KEY = 'solid_dynamics_settings';
const SETTINGS_SECTION_ELEMENTOR_KEY = 'solid_dynamics_elementor';

class SettingsPage {
    function __construct() {
        add_action( 'admin_init', [$this, 'admin_init'] );
        add_action( 'admin_menu', [$this, 'admin_menu'] );
    }

    function admin_init() {
        // Store all plugin settings as serialized array in single wp_options row.
        register_setting(
            SETTINGS_PAGE_KEY,
            SETTINGS_OPTION_KEY,
            [
                'type' => 'object',
            ]
        );

        add_settings_section(
            SETTINGS_SECTION_ELEMENTOR_KEY,
            __( 'Elementor Settings', 'solid-dynamics' ),
            '__return_false',
            SETTINGS_PAGE_KEY
        );
    }

    function admin_menu() {
        add_options_page( 'Solid Dynamics', 'Solid Dynamics', 'manage_options', SETTINGS_PAGE_KEY, [$this, 'options_page'] );
    }

    function options_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( SETTINGS_PAGE_KEY );

                do_settings_sections( SETTINGS_PAGE_KEY );

                submit_button( 'Save Settings' );
                ?>
            </form>
        </div>
        <?php
    }
}