<?php
namespace Solid;

add_filter( 'wpsf_register_settings_solid_dynamics', 'Solid\wpsf_register_settings' );

function wpsf_register_settings( $wpsf_settings ) {
	$wpsf_settings[] = array(
		'section_id'            => 'general',
		'section_title'         => 'General',
		'section_order'         => 10,
		'fields'                => array(
			array(
				'id'      => 'disable_404_permalink_guessing',
				'title'   => '404 Permalink Guessing',
				'desc'    => 'Disable permalink guessing for 404s.',
				'type'    => 'checkbox',
				'default' => 0,
			),
			array(
			    'id'      => 'disable_users_api',
			    'title'   => 'Disable Users API',
			    'desc'    => 'Disable the Users REST API: /wp-json/wp/v2/users',
			    'type'    => 'checkbox',
			    'default' => 0,
			),
		),
	);

	$wpsf_settings[] = array(
		'section_id'            => 'elementor',
		'section_title'         => 'Elementor',
		'section_order'         => 10,
		'fields'                => array(
			array(
				'id'      => 'hide_back_to_wp_editor_button',
				'title'   => '"Back to WordPress Editor" Button',
				'desc'    => 'Hide the "Back to WordPress Editor" button on the edit page.',
				'type'    => 'checkbox',
				'default' => 0,
			),
			array(
				'id'      => 'hide_hello_elementor_page_title',
				'title'   => 'Hello Elementor Page Title',
				'desc'    => 'Hide the page title from the Hello Elementor theme.',
				'type'    => 'checkbox',
				'default' => 0,
			),
			array(
				'id'      => 'wrap_content',
				'title'   => 'Wrap Elementor Content',
				'desc'    => 'Wrap Elementor content with `main#content`.',
				'type'    => 'checkbox',
				'default' => 0,
			),
			array(
				'id'      => 'subtle_fade_in_entrance_animations',
				'title'   => 'Fade in Entrance Animations',
				'desc'    => 'Make Elementor fade in entrance animations more subtle.',
				'type'    => 'checkbox',
				'default' => 0,
			),
		),
	);

	return $wpsf_settings;
}
