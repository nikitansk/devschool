<?php
/**
 * FILTER functions for WPLMS
 *
 * @author      VibeThemes
 * @category    Admin
 * @package     Initialization
 * @version     2.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

class WPLMS_Filters{

    public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new WPLMS_Filters();

        return self::$instance;
    }

    private function __construct(){

		add_filter('woocommerce_enable_setup_wizard',array($this,'wplms_disable_woocommerce_setup_wizard'));
		add_filter( 'woocommerce_output_related_products_args', array($this,'wplms_related_products_args') );
  		
		add_filter( 'bbp_after_get_the_content_parse_args', array($this,'bbp_enable_visual_editor' ));
		add_filter('wplms_sidebar',array($this,'wplms_sidebar_select'),10,2);

		/*==== WORDPRESS SEO COMPATIBILITY ======*/

		add_filter('wpseo_title',array($this,'remove_wpseo_from_buddypress'));
		add_filter('wpseo_pre_analysis_post_content',array($this,'vibe_page_builder_content'),10,2);
		
		add_filter( 'bp_core_fetch_avatar_no_grav', '__return_true' );
		add_filter( 'bp_core_default_avatar_user', array($this,'vibe_custom_avatar' ));
		add_filter('wplms_activity_loop',array($this,'wplms_student_activity'));

		add_filter('get_avatar',array($this,'change_avatar_css'));
		add_filter('widget_text', 'do_shortcode');
		add_filter( 'registration_redirect' , array($this,'vibe_registration_redirect') );

		add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', array($this,'wplms_xprofile_cover_image'), 10, 1 );


    }
    


    function wplms_xprofile_cover_image( $settings = array() ) {
	    $settings['width']  = 1600;
	    $settings['height'] = 600;
	 
	    return $settings;
	}

    function remove_wpseo_from_buddypress($title){
    	global $bp,$post;
    	if(empty($this->bp_pages)){
    		$this->bp_pages = get_option('bp-pages');	
    	}
    		
    	if((function_exists('bp_is_directory') && bp_is_directory()) || in_array($post->ID,$this->bp_pages)){
    		$title = sprintf(_x('%s Directory - %s','Directory Title format','vibe'),ucfirst(bp_current_component()),get_bloginfo('name'));
    	}
    	if (function_exists('bp_is_user') && bp_is_user()){
    		//$title = sprintf(_x('%1s group - %2s','Member Name',ucfirst(bp_get_displayed_user_fullname()),get_bloginfo('name')));
    		$title = ucfirst(bp_get_displayed_user_fullname()).' - '.get_bloginfo('name');
    	}
    	if (function_exists('bp_is_group') && bp_is_group()){
    		//$title = sprintf(_x('%1s group - %2s','Group Name',ucfirst(bp_get_current_group_name()),get_bloginfo('name')));
    		$title = ucfirst(bp_get_current_group_name()).' - '.get_bloginfo('name');
    	}
    	return $title;
    }

    function get_directory_page_id($component){
    	if(empty($this->bp_pages)){
    		$this->bp_pages = get_option('bp-pages');	
    	}
    	
		if(isset($this->bp_pages[$component])){
			return $this->bp_pages[$component];
		}

    }

    function wplms_disable_woocommerce_setup_wizard($setup_wizard){
	  return false;
	}
    function wplms_related_products_args( $args ) {
	  $args['posts_per_page'] = 3; 
	  $args['columns'] = 3;
	  return $args;
	}

	function bbp_enable_visual_editor( $args = array() ) {
	    $args['tinymce'] = true;
	    return $args;
	}

	function wplms_sidebar_select($sidebar,$id = NULL){
	  if(isset($id)){
	    $selected_sidebar=get_post_meta($id,'vibe_sidebar',true);  
	    if(isset($selected_sidebar) && $selected_sidebar){

	        /*=== FOR BACKWARD COMPATIBILITY ===*/
	        if($selected_sidebar == 'mainsidebar' && $sidebar != 'mainsidebar'){
	               $selected_sidebar = $sidebar;
	        }else
	          $sidebar=$selected_sidebar; 
	        /*=== END BACKWARD COMPATIBILITY ===*/
	    }
	  }
	  return $sidebar;
	}


	function vibe_page_builder_content($post_content,$post){

	  	if(get_post_type($post->ID) != 'page')
	    	return $post_content;

	  	$builder_enable = get_post_meta( $post->ID, '_enable_builder', true );
	  	if(!empty($builder_enable)){

		    $builder_layout = get_post_meta( $post->ID, '_builder_settings', true );
		    $add_content = get_post_meta( $post->ID, '_add_content', true );
		  
		        if ( isset($builder_layout) &&  isset($builder_layout['layout_shortcode']) && '' != $builder_layout['layout_shortcode'] && $add_content == 'no') { 
		          $content = $builder_layout['layout_shortcode'];
		        }
		        
		        if ( $builder_layout && '' != $builder_layout['layout_shortcode'] && $add_content == 'yes_top') {
		            $content = $post_content.$builder_layout['layout_shortcode'];
		        }
		        
		        if ( $builder_layout && '' != $builder_layout['layout_shortcode'] && $add_content == 'yes_below') {
		            $content = $builder_layout['layout_shortcode'].$post_content;
		        }
		    $post_content = $content;    
	  	}      
	    return $post_content;
	}

	function vibe_custom_avatar($avatar){
	  	global $bp;
	   	$avatar=vibe_get_option('default_avatar');
	   	if(!isset($avatar) || !$avatar || strlen($avatar)<5)
	    	$avatar = VIBE_URL.'/images/avatar.jpg';
	   	return $avatar;
	}

	function wplms_student_activity($appended){
	  	$student_activity = vibe_get_option('student_activity');
	  	if(!current_user_can('edit_posts') && isset($student_activity) && $student_activity){
	    	$appended .='&user_id='.get_current_user_id();
	  	}
	    
	    return $appended;
	}
	

	function change_avatar_css($class) {
	  	$class = str_replace("class='avatar", "class='retina_avatar zoom animate", $class) ;
	  	return $class;
	}

	function vibe_registration_redirect() {
    	$pageid=vibe_get_option('activation_redirect');
    	return get_permalink($pageid);
	}

}

WPLMS_Filters::init();

function vibe_get_directory_page($component){
	$wf = WPLMS_Filters::init();
	return $wf->get_directory_page_id($component);
}
