=== Solid Dynamics ===
Contributors: soliddigital,lukechinworth,peterajtai
Tags: elementor, dynamic tags, jet engine, macros
Tested up to: 6.7.1
Stable tag: 1.7.0
Requires PHP: 7.0
License: GPLv2

Helpful utilities for Elementor, Jet Engine, and beyond.

== Description ==

This plugin provides an Admin Page called, "Widget Usage" under the "Solid Dynamics" menu that shows the individual posts in which a widget is used. Currently, the Elementor Element Manager only shows the total number of usages of a widget. We're always wondering where those widgets are being used, and "Widget Usage" is the answer to that question.

Solid Dynamics also provides several dynamic tags under the "Solid Dynamics" section in Elementor:

- `Custom Callback`: Call any php function. The current post is passed as the first argument.
- `Menu`: Returns comma-separated post ids of a specific menu.
- `Parent Meta`: Retrieves the meta value of the parent post based on the entered meta key.
- `List Pluck`: Pluck `field` off each item in `list` (`src` meta or option), and join with `sep`.
- `Post Field`: Retrieves custom post field by name.

This plugin also provides several general use and Elementor specific settings under the menu Solid Dynamics. All settings have to be opted in to. Activating the plugin does not activate any of the settings. Activating the plugin does automatically make the dynamic tags listed above available.

General Settings:

- Disable 404 permalink guessing.
- Disable the enumeration of users using the rest API. Disables `/wp-json/wp/v2/users` and `/wp-json/wp/v2/users/:ID`
- Add a sortable column of registration dates to the Users page

Elementor:

- Hide the "Back to WordPress Editor" button from the edit page.
- Hide the page title from the Hello Elementor theme.
- Wrap content with `main#content`.
- Make fade in entrance animations more subtle.

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

= 1.8.0 =
2025-01-19
Adding setting to add to the Users page a sortable registration date column

= 1.7.0 =
2024-11-26
- Feature: Update elementor fade in animation css.

= 1.6.2 =
2024-11-24
- Feature: Moving "Solid Dynamics" to its own menu page
- Patch: Ensuring "Widget Usage" menu option is visible

= 1.6.1 =
2024-11-23
- Patch: Changelog update

= 1.6.0 =
2024-11-23
- Feature: `Widget Usage`

= 1.5.0 =
2023-04-08
- Feature: add `Disable Users REST API`
- Feature: add `Post Field` dynamic tag.

= 1.3.4 =
2022-12-09
- Fix fatal error on get_class call.

= 1.3.3 =
2022-12-09
- Add support for term and user meta to list pluck tag.

= 1.3.2 =
2022-12-05
- Add support for wp_options to list pluck tag.

= 1.3.0 =
2022-11-01
- Feature: add settings: Disable 404 permalink guessing; Hide the page title from the Hello Elementor theme; Wrap Elementor content with `main#content`; Make Elementor fade in entrance animations more subtle.

= 1.2.0 =
2022-09-02
- Feature: add settings page with option to remove elementor's "back to wp editor" button.

= 1.1.3 =
2022-08-13
- Bug fix: Do not try to load JetEngine if plugin is not preseent

= 1.1.2 =
2022-08-09
- Bug fix: Escape custom callback output with wp_kses_post since it could include html.
- Bug fix: Escape the menu output with esc_html since it should only be comma-separated ids.

= 1.1.0 =
2022-03-16
- Feature: Add menu dynamic tag.

= 1.0.0 =
2021-06-15
- Feature: Initial release
