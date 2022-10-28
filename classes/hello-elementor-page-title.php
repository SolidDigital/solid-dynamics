<?php
namespace Solid;

class HelloElementorPageTitle {
    const SETTING_KEY = 'hello_elementor_hide_page_title';
    const SETTING_DEFAULT = 0;

    function __construct() {
        add_action( 'admin_init', [$this, 'admin_init'] );
        add_action( 'init', [$this, 'init'] );
    }

    function admin_init() {
        add_settings_field(
            self::SETTING_KEY,
            __( 'Hello Elementor Page Title', 'solid-dynamics' ),
            [$this, 'settings_page_field'],
            SETTINGS_PAGE_KEY,
            SETTINGS_SECTION_ELEMENTOR_KEY
        );
    }

    function settings_page_field($args) {
        $setting = self::get_setting();
        $name = SETTINGS_OPTION_KEY . '[' . self::SETTING_KEY . ']';

        ?>
        <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0">
        <label for="<?php echo esc_attr( self::SETTING_KEY ); ?>">
            <input
                type="checkbox"
                id="<?php echo esc_attr( self::SETTING_KEY ); ?>"
                name="<?php echo esc_attr( $name ); ?>"
                value="1"
                <?php checked( $setting, 1 ) ?>
            />
            <?php esc_html_e( 'Hide the page title from the Hello Elementor theme.', 'solid-dynamics' ); ?>
        </label>
        <?php
    }

    function init() {
        if (self::get_setting()) {
            add_filter( 'hello_elementor_page_title', '__return_false');
        }
    }

    static function get_setting() {
        $settings = get_option( SETTINGS_OPTION_KEY );

        return isset($settings[self::SETTING_KEY]) ? $settings[self::SETTING_KEY] : self::SETTING_DEFAULT;
    }
}
