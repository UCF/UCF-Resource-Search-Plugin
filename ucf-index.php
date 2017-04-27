<?php
/*
Plugin Name: UCF A-Z Index
Description:
Version: 1.0.0
Author: UCF Web Communications
License: GPL3
*/


if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'plugins_loaded', function() {

	define( 'UCF_INDEX__PLUGIN_FILE', __FILE__ );

	require_once 'includes/ucf-index-common.php';
	require_once 'shortcodes/ucf-index-shortcode.php';

} );

?>
