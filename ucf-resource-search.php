<?php
/*
Plugin Name: UCF Resource Search
Description:
Version: 1.0.0
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

	add_action( 'admin_menu', array( 'UCF_Resource_Search_Config', 'add_options_page' ) );

} );

?>
