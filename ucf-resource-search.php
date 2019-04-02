<?php
/*
Plugin Name: UCF Resource Search
Description: Provides a custom post type, shortcode, functions, and default styles for displaying a resource search input and list of resources.
Version: 1.0.5
Author: UCF Web Communications
License: GPL3
*/


if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'plugins_loaded', function() {

	define( 'UCF_Resource_Search__PLUGIN_FILE', __FILE__ );

	require_once 'includes/ucf-resource-search-config.php';
	require_once 'includes/ucf-resource-link-tax.php';
	require_once 'includes/ucf-resource-search-common.php';
	require_once 'includes/ucf-resource-link-posttype.php';
	require_once 'shortcodes/ucf-resource-search-shortcode.php';

	require_once 'layouts/ucf-resource-link-classic.php';
	require_once 'layouts/ucf-resource-link-card.php';

	add_action( 'admin_menu', array( 'UCF_Resource_Search_Config', 'add_options_page' ) );

} );

?>
