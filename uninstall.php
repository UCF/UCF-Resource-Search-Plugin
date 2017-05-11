<?php
/**
 * Handles uninstallation logic.
 **/
 if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

require_once 'includes/ucf-resource-search-config.php';

// Delete options
UCF_Resource_Search_Config::delete_options();
?>
