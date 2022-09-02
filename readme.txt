=== Solid Dynamics ===
Contributors: soliddigital,lukechinworth,peterajtai
Tags: elementor, dynamic tags, jet engine, macros
Tested up to: 6.0
Stable tag: 1.2.0
Requires PHP: 7.0
License: GPLv2

Helpful utilities for Elementor, Jet Engine, and beyond.

== Description ==

This plugin provides several dynamic tags under the "Solid Dynamics" section:

- `Custom Callback`: Calls the entered function, passing the current post as the first argument.
- `Menu`: Returns the ids of the post of a specific menu id.
- `Parent Meta`: Retrieves the meta value of the parent post based on the entered meta key.

This plugin also provides several settings at Settings > Solid Dynamics:

- Elementor "Back to WordPress Editor" Button - Hide this button to prevent editors from reverting elementor page back to wp editor.

== Contributing ==

The code is managed on [github](https://github.com/SolidDigital/solid-dynamics), and synced to [WordPress' Solid Dynamics SVN repo](https://plugins.trac.wordpress.org/browser/solid-dynamics/).

== Installation ==

1. Download, unzip, and upload the plugin folder to `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Custom Callback Dynamic Tag using the wp function `wp_get_post_parent_id`.
2. Custom Menu Dynamic Tag
3. The various Dynamic Tag options

== Changelog ==

= 1.2.0 =
- Feature: add settings page with option to remove elementor's "back to wp editor" button.

= 1.1.3 =
- Bug fix: Do not try to load JetEngine if plugin is nto preseent

= 1.1.2 =
- Bug fix: Escape custom callback output with wp_kses_post since it could include html.
- Bug fix: Escape the menu output with esc_html since it should only be comma-separated ids.

= 1.1.0 =
- Feature: Add menu dynamic tag.

= 1.0.0 =
- Feature: Initial release
