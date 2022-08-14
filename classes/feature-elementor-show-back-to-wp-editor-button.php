<?php
namespace Solid;

class FeatureElementorShowBackToWPEditorButton {
    const SETTING_KEY = 'elementor_show_back_to_wp_editor_button';
    const SETTING_DEFAULT = 1;

    function __construct() {
        add_action( 'admin_init', [$this, 'admin_init'] );
        add_action( 'admin_head', [$this, 'admin_head'] );
    }

    function admin_init() {
        add_settings_field(
            self::SETTING_KEY,
            __( '"Back to WordPress Editor" Button', 'solid-dynamics' ),
            [$this, 'settings_page_field'],
            SETTINGS_PAGE_KEY,
            SETTINGS_SECTION_ELEMENTOR_KEY
        );
    }

    function settings_page_field($args) {
        // Get the value of the setting we've registered with register_setting()
        $setting = self::get_setting();
        $name = SETTINGS_OPTION_KEY . '[' . self::SETTING_KEY . ']';

        ?>
        <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0">
        <input
            type="checkbox"
            id="<?php echo esc_attr( self::SETTING_KEY ); ?>"
            name="<?php echo esc_attr( $name ); ?>"
            value="1"
            <?php checked( $setting, 1 ) ?>
        />
        <label for="<?php echo esc_attr( self::SETTING_KEY ); ?>">
            <?php esc_html_e( 'Show the "Back to WordPress Editor" button on the post page.', 'solid-dynamics' ); ?>
        </label>
        <?php
    }

    function admin_head() {
        $screen = get_current_screen();

        if ($screen->base === 'post' && !self::get_setting()) {
            ?>
            <style>
                body.elementor-editor-active #elementor-switch-mode {
                    display: none;
                }
            </style>
            <?php
        }
    }

    static function get_setting() {
        $settings = get_option( SETTINGS_OPTION_KEY );

        return isset($settings[self::SETTING_KEY]) ? $settings[self::SETTING_KEY] : self::SETTING_DEFAULT;
    }
}