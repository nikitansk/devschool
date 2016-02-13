<?php
/*
Plugin Name: BP Profile Cover
Plugin URI: http://www.VibeThemes.com
Description: Add Profile covers to Member and Group PRofiles (BuddyPress 2.3+)
Version: 1.2.1
Requires at least: WP 3.8, BuddyPress 2.3
Tested up to: 2.0.1
License: GPLv2
Contributors:Mr.Vibe,vibethemes
Author URI: http://www.VibeThemes.com
Network: true
*/

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_profile_cover_init() {
	// Because our loader file uses BP_Attachment API, it requires BP 2.3 or greater.
	if ( version_compare( BP_VERSION, '2.3', '>' ) ){
		require( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class.settings.php' );
		require( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class.attachment.php' );
		require( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/class.functions.php' );
		
	}
}
add_action( 'bp_include', 'bp_profile_cover_init' );

add_action('plugins_loaded','bp_profile_cover_translations');
function bp_profile_cover_translations(){
    $locale = apply_filters("plugin_locale", get_locale(), 'bp-profile-cover');
    $lang_dir = dirname( __FILE__ ) . '/languages/';
    $mofile        = sprintf( '%1$s-%2$s.mo', 'bp-profile-cover', $locale );
    $mofile_local  = $lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

    if ( file_exists( $mofile_global ) ) {
        load_textdomain( 'bp-profile-cover', $mofile_global );
    } else {
        load_textdomain( 'bp-profile-cover', $mofile_local );
    }   
}