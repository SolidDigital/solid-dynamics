<?php

add_filter( 'wpsf_register_settings_solid_dynamics', 'wpsf_tabless_settings' );

function wpsf_tabless_settings( $wpsf_settings ) {
	$wpsf_settings[] = array(
		'section_id'            => 'elementor',
		'section_title'         => 'Elementor',
		'section_order'         => 10,
		'fields'                => array(
			array(
				'id'      => 'hide_back_to_wp_editor_button',
				'title'   => '"Back to WordPress Editor" Button',
				'desc'    => 'Hide the "Back to WordPress Editor" button on the post page.',
				'type'    => 'checkbox',
				'default' => false,
			),
			array(
				'id'      => 'hide_hello_elementor_page_title',
				'title'   => 'Hello Elementor Page Title',
				'desc'    => 'Hide the page title from the Hello Elementor theme.',
				'type'    => 'checkbox',
				'default' => false,
			),
		),
	);

	return $wpsf_settings;
}
