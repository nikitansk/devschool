<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Essentials
include_once 'includes/config.php';
include_once 'includes/init.php';

// Register & Functions
include_once 'includes/register.php';
include_once 'includes/actions.php';
include_once 'includes/filters.php';
include_once 'includes/func.php';
include_once 'includes/ratings.php';
// Customizer
include_once 'includes/customizer/customizer.php';
include_once 'includes/customizer/css.php';
include_once 'includes/vibe-menu.php';
include_once 'includes/notes-discussions.php';


if ( function_exists('bp_get_signup_allowed')) {
    include_once 'includes/bp-custom.php';
}

include_once '_inc/ajax.php';
include_once 'includes/buddydrive.php';
//Widgets
include_once('includes/widgets/custom_widgets.php');
if ( function_exists('bp_get_signup_allowed')) {
 include_once('includes/widgets/custom_bp_widgets.php');
}

include_once('includes/widgets/advanced_woocommerce_widgets.php');
include_once('includes/widgets/twitter.php');
include_once('includes/widgets/flickr.php');

//Misc
include_once 'includes/extras.php';
include_once 'includes/tincan.php';
include_once 'setup/wplms-install.php';

// Options Panel
get_template_part('vibe','options');


add_filter('comment_form_defaults', 'tinymce_comment_enable');
function tinymce_comment_enable ( $args ) {
    ob_start();
    wp_editor('', 'comment', array('tinymce'));
    $args['comment_field'] = ob_get_clean();
    return $args;
}


add_filter( 'comment_form_defaults', 'remove_comment_form_allowed_tags' );
function remove_comment_form_allowed_tags( $defaults ) {

	$defaults['comment_notes_after'] = '';
	return $defaults;

}

function override_mce_options($initArray) {
    $opts = '*[*]';
    $initArray['valid_elements'] = $opts;
    $initArray['extended_valid_elements'] = $opts;
    return $initArray;
}
add_filter('tiny_mce_before_init', 'override_mce_options');

// Allow tag PRE in comment post (it need for notes ans submissoin system in assignment module, for highlight syntax code)

$allowedtags['pre'] = array();
