<?php
namespace Solid;

class ElementorWrapContent {
    const SETTING_KEY = 'elementor_wrap_content';
    const SETTING_DEFAULT = 0;

    function __construct() {
        add_action( 'admin_init', [$this, 'admin_init'] );
        add_action( 'init', [$this, 'init'] );
    }

    function admin_init() {
        add_settings_field(
            self::SETTING_KEY,
            __( 'Wrap Elementor Content', 'solid-dynamics' ),
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
            <?php esc_html_e( 'Wrap Elementor content with `main#content`.', 'solid-dynamics' ); ?>
        </label>
        <?php
    }

    function init() {
        if (self::get_setting()) {
            add_action( 'elementor/theme/before_do_single', [$this, 'main_open'] );
            add_action( 'elementor/theme/after_do_single', [$this, 'main_close'] );

            add_action( 'elementor/theme/before_do_archive', [$this, 'main_open'] );
            add_action( 'elementor/theme/after_do_archive', [$this, 'main_close'] );
        }
    }

    function main_open() {
        ?>
        <main id="content">
        <?php
    }

    function main_close() {
        ?>
        </main>
        <?php
    }
    static function get_setting() {
        $settings = get_option( SETTINGS_OPTION_KEY );

        return isset($settings[self::SETTING_KEY]) ? $settings[self::SETTING_KEY] : self::SETTING_DEFAULT;
    }
}
