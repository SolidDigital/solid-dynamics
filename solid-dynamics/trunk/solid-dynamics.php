<?php

/**
 * Plugin Name:       Solid Dynamics
 * Description:       Custom callbacks for elementor dynamic tags and jet engine macros.
 * Version:           1.0.0
 * Author:            Solid Digital
 * Author URI:        https://www.soliddigital.com
 * License:           GPLv2
 * Text Domain:       solid-dynamics
 * Domain Path:       /languages
 */

namespace Solid;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'elementor/dynamic_tags/register_tags', function( $dynamic_tags ) {
    // In our Dynamic Tag we use a group named request-variables so we need
    // To register that group as well before the tag
    \Elementor\Plugin::$instance->dynamic_tags->register_group( 'solid-dynamics', [
        'title' => 'Solid Dynamics'
    ] );

    // Include the Dynamic tag class file
    include_once( __DIR__ . "/classes/parent-meta.php" );
    include_once( __DIR__ . "/classes/parent-meta-image.php" );
    include_once( __DIR__ . "/classes/custom-callback.php" );

    // Finally register the tag
    $dynamic_tags->register_tag( '\Solid\ParentMeta' );
    $dynamic_tags->register_tag( '\Solid\ParentMetaImage' );
    $dynamic_tags->register_tag( '\Solid\CustomCallback' );
} );

include_once( __DIR__ . "/classes/custom-callback-macro.php" );

new CustomCallbackMacro();
