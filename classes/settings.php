<?php
namespace Solid;

class Settings {
    function __construct() {
        add_action( 'admin_init', [$this, 'admin_init'] );
        add_action( 'admin_menu', [$this, 'admin_menu'] );
    }

    function admin_init() {
        register_setting( 'solid_dynamics', 'solid_dynamics_options' );

        add_settings_section(
            'solid_dynamics_elementor',
            __( 'Elementor Settings', 'solid-dynamics' ),
            [$this, 'section_elementor'],
            'solid_dynamics'
        );

        add_settings_field(
            'include_back_to_wp_button', // As of WP 4.6 this value is used only internally.
            __( 'Include Elementor "Back to WordPress" Button', 'solid-dynamics' ),
            [$this, 'field_elementor_include_back_to_wp_button'],
            'solid_dynamics',
            'solid_dynamics_elementor',
            array(
                'label_for'         => 'include_back_to_wp_button',
                'class'             => 'solid_dynamics_row',
            )
        );
    }

    function section_elementor( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Elementor Section', 'solid-dynamics' ); ?></p>
        <?php
    }

    function field_elementor_include_back_to_wp_button($args) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'solid_dynamics_options' );
        ?>
        <input
            type="checkbox"
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            name="solid_dynamics[<?php echo esc_attr( $args['label_for'] ); ?>]"
            value="1"
            <?php echo isset( $options[ $args['label_for'] ] ) ? ( checked( $options[ $args['label_for'] ], 'red', false ) ) : ( '' ); ?>
        />
        <p class="description">
            <?php esc_html_e( 'This controls the display of the "<- Back to WordPress" button on the post edit page.', 'solid-dynamics' ); ?>
        </p>
        <?php
    }

    function admin_menu() {
        add_options_page( 'Solid Dynamics', 'Solid Dynamics', 'manage_options', 'solid_dynamics', [$this, 'options_page'] );
    }

    function options_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'solid_dynamics_messages', 'solid_dynamics_message', __( 'Settings Saved', 'solid-dynamics' ), 'updated' );
        }

        // show error/update messages
        settings_errors( 'solid_dynamics_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "wporg"
                settings_fields( 'solid_dynamics' );
                // output setting sections and their fields
                // (sections are registered for "wporg", each field is registered to a specific section)
                do_settings_sections( 'solid_dynamics' );
                // output save settings button
                submit_button( 'Save Settings' );
                ?>
            </form>
        </div>
        <?php
    }
}