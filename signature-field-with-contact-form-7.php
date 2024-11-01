<?php
/**
* Plugin Name: Signature Field With Contact Form 7
* Description: This plugin allows create Digital Signature With Contact form 7 plugin.
* Version: 1.0
* Copyright: 2023
* Text Domain: signature-field-with-contact-form-7
*/


if (!defined('SFWCF7_PLUGIN_DIR')) {
    define('SFWCF7_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('SFWCF7_SIGNATURE_URL')) {
  define('SFWCF7_SIGNATURE_URL',plugins_url('', __FILE__));
}



add_action('wpcf7_enqueue_scripts', 'SFWCF7_load_scripts');
function SFWCF7_load_scripts(){
	wp_enqueue_script('jquery');
    wp_enqueue_script('dswcf7-signpad', SFWCF7_SIGNATURE_URL.'/public/js/signature-pad.min.js');
    wp_enqueue_script('dswcf7-sign-js', SFWCF7_SIGNATURE_URL.'/public/js/design.js', array('jquery'), time() );
    wp_enqueue_style('dswcf7-signpad-style', SFWCF7_SIGNATURE_URL.'/public/css/signature-pad.css');
}

add_action( 'admin_enqueue_scripts',  'SFWCF7_load_admin_script_style');
function SFWCF7_load_admin_script_style() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker-alpha', SFWCF7_SIGNATURE_URL.'/admin/js/wp-color-picker-alpha.js', array( 'wp-color-picker' ), '1.0.0', true );
}

// Load Plugin in admin init
add_action( 'admin_init',  'SFWCF7_load_plugin', 11 );
function SFWCF7_load_plugin() {
    if ( ! ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) ) {
        set_transient( get_current_user_id() . 'sfwcf7error', 'message' );
    }
}

// Include function files
include_once(SFWCF7_PLUGIN_DIR.'includes/admin.php');
include_once(SFWCF7_PLUGIN_DIR.'includes/frontend.php');

// Error returns when Contact Form 7 is not installed
add_action( 'admin_notices', 'SFWCF7_install_error');
function SFWCF7_install_error() {
    if ( get_transient( get_current_user_id() . 'sfwcf7error' ) ) {

        deactivate_plugins( plugin_basename( __FILE__ ) );

        delete_transient( get_current_user_id() . 'sfwcf7error' );

        echo '<div class="error"><p> This plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=contact+form+7">Contact Form 7</a> plugin installed and activated.</p></div>';
    }
}