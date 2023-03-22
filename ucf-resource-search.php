<?php
/*
Plugin Name: UCF Resource Search
Description: Provides a custom post type, shortcode, functions, and default styles for displaying a resource search input and list of resources.
Version: 1.0.9
Author: UCF Web Communications
License: GPL3
*/


if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'UCF_RESOURCE_SEARCH__PLUGIN_FILE', __FILE__ );
define( 'UCF_RESOURCE_SEARCH__PLUGIN_URL', plugins_url( basename( dirname( __FILE__ ) ) ) );
define( 'UCF_RESOURCE_SEARCH__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UCF_RESOURCE_SEARCH__STATIC_URL', UCF_RESOURCE_SEARCH__PLUGIN_URL . '/static' );
define( 'UCF_RESOURCE_SEARCH__SCRIPT_URL', UCF_RESOURCE_SEARCH__STATIC_URL . '/js' );
define( 'UCF_RESOURCE_SEARCH__STYLES_URL', UCF_RESOURCE_SEARCH__STATIC_URL . '/css' );

add_action( 'plugins_loaded', function() {

	require_once UCF_RESOURCE_SEARCH__PLUGIN_DIR . 'includes/ucf-resource-search-config.php';
	require_once UCF_RESOURCE_SEARCH__PLUGIN_DIR . 'includes/ucf-resource-link-tax.php';
	require_once UCF_RESOURCE_SEARCH__PLUGIN_DIR . 'includes/ucf-resource-search-common.php';
	require_once UCF_RESOURCE_SEARCH__PLUGIN_DIR . 'includes/ucf-resource-link-posttype.php';
	require_once UCF_RESOURCE_SEARCH__PLUGIN_DIR . 'shortcodes/ucf-resource-search-shortcode.php';

	require_once UCF_RESOURCE_SEARCH__PLUGIN_DIR . 'layouts/ucf-resource-link-classic.php';
	require_once UCF_RESOURCE_SEARCH__PLUGIN_DIR . 'layouts/ucf-resource-link-card.php';

	add_action( 'admin_menu', array( 'UCF_Resource_Search_Config', 'add_options_page' ) );

} );

?>
