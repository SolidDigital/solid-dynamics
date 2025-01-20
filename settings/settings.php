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
				'page_title'  => esc_html__( 'Settings', 'solid-dynamics' ),
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

        if ($settings['general_disable_users_api']) {
            add_filter( 'rest_endpoints', [$this, 'disable_user_endpoint'] );
        }

        if ($settings['general_sort_users_by_registration_date']) {
            // Idea from https://rudrastyh.com/wordpress/sortable-date-user-registered-column.html
            add_filter( 'manage_users_columns', [$this, 'add_registration_date_column'] );
            add_filter( 'manage_users_custom_column', [$this, 'get_registration_date'], 10, 3 );
            // Make registration date sortable
            add_filter( 'manage_users_sortable_columns', [$this,'make_registration_date_sortable'] );
        }

		if ($settings['elementor_hide_back_to_wp_editor_button']) {
			add_action( 'admin_head', [$this, 'elementor_hide_back_to_wp_editor_button'] );
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

		if ($settings['elementor_subtle_fade_in_entrance_animations']) {
			add_action( 'wp_enqueue_scripts', [$this, 'elementor_subtle_fade_in_entrance_animations'] );
		}
	}

	function elementor_hide_back_to_wp_editor_button() {
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

    function disable_user_endpoint( $endpoints ) {

        if ( isset( $endpoints['/wp/v2/users'] ) ) {
            unset( $endpoints['/wp/v2/users'] );
        }

        // to remove endpoints like /wp-json/wp/v2/users/4
        if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
            unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
        }

        return $endpoints;
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

	function elementor_subtle_fade_in_entrance_animations() {
		wp_enqueue_style('sd-elementor-subtle-fade-in-entrance-animations', plugin_dir_url(__DIR__) . 'assets/elementor-subtle-fade-in-entrance-animations.css', ['e-animations'], '1.0.0');
	}

	function add_registration_date_column( $columns ) {
        $columns[ 'registration_date' ] = 'Registration Date';
        return $columns;
    }

    function get_registration_date( $row_output, $column_id_attr, $user ) {
        $date_format = 'j M, Y H:i';
        if ($column_id_attr == 'registration_date') {
            $row_output = date( $date_format, strtotime( get_the_author_meta( 'registered', $user ) ) );
        }
        return $row_output;
    }

    function make_registration_date_sortable( $columns ) {
        return wp_parse_args( array( 'registration_date' => 'registered' ), $columns );
    }
}
