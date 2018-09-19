<?php
/**
 * Cleans up the plugin options.
 *
 * @package      Amazon Auto Links
 * @copyright    Copyright (c) 2013-2015, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/*
 * Plugin specific constant.
 * We are going to load the main file to get the registry class. And in the main file,
 * if this constant is set, it will return after declaring the registry class.
 **/
if ( ! defined( 'DOING_PLUGIN_UNINSTALL' ) ) {
    define( 'DOING_PLUGIN_UNINSTALL', true  );
}

/**
 * Set the main plugin file name here.
 */
$_sMaingPluginFileName  = 'externals-feed-personalizer.php';
if ( file_exists( dirname( __FILE__ ). '/' . $_sMaingPluginFileName ) ) {
   include( $_sMaingPluginFileName );
}

if ( ! class_exists( 'ExternalsFeedPersonalizer_Registry' ) ) {
    return;
}

// Remove user meta keys used by the plugin
foreach( ExternalsFeedPersonalizer_Registry::$aUserMetas as $_sMetaKey => $_v ) {
    delete_metadata(
        'user',    // the user meta type
        0,  // does not matter here as deleting them all
        $_sMetaKey,
        '', // does not matter as deleting
        true // whether to delete all
    );
}
