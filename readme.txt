=== UCF Resource Search Plugin ===
Contributors: ucfwebcom
Tags: ucf, resource
Requires at least: 4.7.3
Tested up to: 4.9.6
Stable tag: 1.0.4
License: GPLv3 or later
License URI: http://www.gnu.org/copyleft/gpl-3.0.html

Provides a custom post type, shortcode, functions, and default styles for displaying a resource search input and list of resources.


== Description ==

The resource search and links are added to pages using a ucf-resource-search shortcode.

The ucf-resource-search shortcode has several options:
* post_type_name - post, page, etc
* taxonomy - category
* meta_key
* meta_value
* show_empty_sections - true|false
* non_alpha_section_name - Other
* column_width - col-md-4 col-sm-4
* column_count - 3
* order_by - title
* order - ASC, DESC
* show_sorting - true|false
* default_sorting - term
* show_sorting - true|false
* show_uncategorized - true|false
* uncategorized_term_name - Uncategorized
* layout - classic or card

== Installation ==

= Manual Installation =
1. Upload the plugin files (unzipped) to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the "Plugins" screen in WordPress
3. Configure plugin settings from the WordPress admin under "Settings > UCF Resource Search".

= WP CLI Installation =
1. `$ wp plugin install --activate https://github.com/UCF/UCF-Resource-Search-Plugin/archive/master.zip`.  See [WP-CLI Docs](http://wp-cli.org/commands/plugin/install/) for more command options.
2. Configure plugin settings from the WordPress admin under "Settings > UCF Resource Search".

== Dependencies ==

* Athena-Framework or Bootstrap 4 (if using the card layout)
* FontAwesome (if using the card layout)

== Changelog ==

= 1.0.4 =
* Added card layout option for ucf-resource-search shortcode
* Added fields for social links

= 1.0.3 =
* Updated plugin description

= 1.0.2 =
* Fixed PHP notices
* Removed Athena classes
* Fixed search

= 1.0.0 =
* Initial release


== Upgrade Notice ==

n/a


== Installation Requirements ==

None


== Development & Contributing ==

NOTE: this plugin's readme.md file is automatically generated.  Please only make modifications to the readme.txt file, and make sure the `gulp readme` command has been run before committing readme changes.
