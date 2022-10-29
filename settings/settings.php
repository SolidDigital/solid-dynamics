<?php
namespace Solid;

class Settings {
	private $wpsf;

	public function __construct() {

		// Include and create a new WordPressSettingsFramework.
		require_once __DIR__ . '/../wp-settings-framework/wp-settings-framework.php';
		$this->wpsf = new \WordPressSettingsFramework( __DIR__ . '/settings-fields.php', 'solid_dynamics' );

		// Add admin menu.
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );

		// Add an optional settings validation filter (recommended).
		add_filter( $this->wpsf->get_option_group() . '_settings_validate', array( &$this, 'validate_settings' ) );

		// Settings behavior hooks.
		add_action( 'admin_head', [$this, 'admin_head'] );
	}

	public function add_settings_page() {
		$this->wpsf->add_settings_page(
			array(
        'parent_slug' => 'options-general.php',
				'page_title'  => esc_html__( 'Solid Dynamics', 'solid-dynamics' ),
				'menu_title'  => esc_html__( 'Solid Dynamics', 'solid-dynamics' ),
				'capability'  => 'manage_options',
			)
		);
	}

	public function validate_settings( $input ) {
		// Do your settings validation here
		// Same as $sanitize_callback from http://codex.wordpress.org/Function_Reference/register_setting.
		return $input;
	}

	function admin_head() {
		$setting = wpsf_get_setting( 'solid_dynamics', 'elementor', 'hide_back_to_wp_editor_button' );

		$screen = get_current_screen();

		if ($screen->base === 'post' && $setting) {
			?>
			<style>
				body.elementor-editor-active #elementor-switch-mode {
					display: none;
				}
			</style>
			<?php
		}
	}
}
