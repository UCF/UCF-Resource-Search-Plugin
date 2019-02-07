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
* title - the title of the section to be displayed


== Installation ==

= Manual Installation =
1. Upload the plugin files (unzipped) to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the "Plugins" screen in WordPress
3. Configure plugin settings from the WordPress admin under "Settings > UCF Resource Search".

= WP CLI Installation =
1. `$ wp plugin install --activate https://github.com/UCF/UCF-Resource-Search-Plugin/archive/master.zip`.  See [WP-CLI Docs](http://wp-cli.org/commands/plugin/install/) for more command options.
2. Configure plugin settings from the WordPress admin under "Settings > UCF Resource Search".


== Changelog ==

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
