=== Solid Dynamics ===
Contributors: soliddigital,lukechinworth,peterajtai
Tags: elementor, dynamic tags, jet engine, macros
Tested up to: 6.0.1
Stable tag: 1.1.3
License: GPLv2

Custom callbacks for elementor dynamic tags and jet engine macros.

The code is managed in [github/soliddigital/solid-dynamics](https://github.com/SolidDigital/solid-dynamics) and synced to [WordPress' Solid Dynamics SVN repo](https://plugins.trac.wordpress.org/browser/solid-dynamics/).

== Description ==

This plugin provides several dynamic tags. They're all found under the "Solid Dynamics" section.

- `Custom Callback`: An Elementor dynamic tag and jet engine macro to allow you to call any function you want. Write the function in functions.php, then call it from Elementor. Helpful for teams of developers and designers working together.
- `Menu`: Returns the ids of the post of a specific menu id
- `Parent Meta`: Retrieves the meta value of the parent post based on the entered meta key

Also, the plugin passes the current post as the first argument to the function, helpful for built-in wp functions that take the post as the first argument.

== Installation ==

1. Download, unzip, and upload the plugin folder to `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Custom Callback Dynamic Tag using the wp function `wp_get_post_parent_id`.
2. Custom Menu Dynamic Tag
3. The various Dynamic Tag options

== Changelog ==

= 1.1.2 =
* Escape custom callback output with wp_kses_post since it could include html.
* Escape the menu output with esc_html since it should only be comma-separated ids.

= 1.1.0 =
* Add menu dynamic tag.

= 1.0.0 =
* Inital release
