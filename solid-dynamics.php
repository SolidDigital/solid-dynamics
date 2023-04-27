<?php

/**
 * Plugin Name:       Solid Dynamics
 * Description:       Helpful utilities for Elementor, Jet Engine, and beyond.
 * Version:           1.5.3
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

require_once( __DIR__ . "/classes/elementor-dynamic-tags.php" );

new ElementorDynamicTags();

require_once( __DIR__ . "/classes/jet-engine-macros.php" );

new JetEngineMacros();

require_once( __DIR__ . "/settings/settings.php" );

new Settings();
