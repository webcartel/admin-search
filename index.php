<?php

/*
Plugin Name: Admin search
Description: Admin search
Plugin URI: http://#
Author: Admin search
Author URI: http://#
Version: 1.0
License: GPL2
*/

set_time_limit(1000);

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WCST_ADMIN_SEARCH_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'WCST_ADMIN_SEARCH_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCST_ADMIN_SEARCH_WP_UPLOADS_DIR_PATH', wp_upload_dir()['basedir'] );



function wcst_admin_search_activate() {

}
register_activation_hook( __FILE__, 'wcst_admin_search_activate' );


function wcst_admin_search_deactivate() {

}
register_deactivation_hook(  __FILE__, 'wcst_admin_search_deactivate' );




include('inc/admin_side.php');