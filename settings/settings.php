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
		add_action( 'init', [$this, 'init'] );
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

	// Get settings and apply hooks as needed.
	function init() {
		$settings = $this->wpsf->get_settings();

		if ($settings['general_disable_404_permalink_guessing']) {
			add_filter('do_redirect_guess_404_permalink', '__return_false');
		}

		if ($settings['elementor_hide_back_to_wp_editor_button']) {
			add_action( 'admin_head', [$this, 'admin_head'] );
		}

		if ($settings['elementor_hide_hello_elementor_page_title']) {
			add_filter( 'hello_elementor_page_title', '__return_false');
		}

		if ($settings['elementor_wrap_content']) {
			add_action( 'elementor/theme/before_do_single', [$this, 'main_open'] );
			add_action( 'elementor/theme/after_do_single', [$this, 'main_close'] );

			add_action( 'elementor/theme/before_do_archive', [$this, 'main_open'] );
			add_action( 'elementor/theme/after_do_archive', [$this, 'main_close'] );
		}
	}

	function admin_head() {
		$screen = get_current_screen();

		if ($screen->base === 'post') {
			?>
			<style>
				body.elementor-editor-active #elementor-switch-mode {
					display: none;
				}
			</style>
			<?php
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


}
