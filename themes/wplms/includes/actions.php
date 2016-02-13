<?php
/**
 * Action functions for WPLMS
 *
 * @author      VibeThemes
 * @category    Admin
 * @package     Initialization
 * @version     2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;


class WPLMS_Actions{

    public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new WPLMS_Actions();

        return self::$instance;
    }

    private function __construct(){
    	
		add_action('init',array($this,'wplms_removeHeadLinks'));

		add_action('wp_head',array($this,'include_child_theme_styling'));

		add_action('template_redirect',array($this,'site_lock'),1);

		add_action( 'wp_ajax_reset_googlewebfonts',array($this,'reset_googlewebfonts' ));
		
          
		add_action( 'wp_ajax_import_data',array($this,'import_data' ));
		add_action('wplms_be_instructor_button',array($this,'wplms_be_instructor_button'));

		add_action( 'pre_get_posts', array($this,'course_search_results' ));

		add_action(	'template_redirect',array($this,'vibe_check_access_check'));
		add_action( 'template_redirect', array($this,'vibe_check_course_archive' ));
		add_action( 'template_redirect', array($this,'vibe_product_woocommerce_direct_checkout' ));
		add_action('woocommerce_order_item_name',array($this,'vibe_view_woocommerce_order_course_details'),2,100);
		
		add_action('woocommerce_share',array($this,'wplms_social_buttons_on_product'),1000);
		add_action('bp_core_activated_user',array($this,'vibe_redirect_after_registration'),99,3);

		// Course Actions
		add_action('wplms_course_unit_meta',array($this,'vibe_custom_print_button'));
		add_action('wplms_course_start_after_time',array($this,'wplms_course_progressbar'),1,2);
		add_action('wp_ajax_record_course_progress',array($this,'wplms_course_progress_record'));

		/*=== Profile Layout 3 === */
		add_action('bp_before_member_body',array($this,'member_layout_3_before_item_tabs'));
		add_action('wplms_after_single_item_list_tabs',array($this,'member_layout_3_after_item_tabs'));
		add_action('bp_after_member_body',array($this,'member_layout_3_end_body'));

		add_action('wplms_before_single_group_item_list_tabs',array($this,'group_layout_3_before_item_tabs'));
		add_action('wplms_after_single_group_item_list_tabs',array($this,'group_layout_3_after_item_tabs'));
		add_action('bp_after_group_body',array($this,'group_layout_3_end_body'));


		add_action('wp_ajax_nopriv_forgot_wp_password',array($this,'forgot_password'));

		/* ==== WooCommerce MY Orders ==== */
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || (function_exists('is_plugin_active_for_network') && is_plugin_active_for_network( 'woocommerce/woocommerce.php'))) {
			add_action( 'bp_setup_nav', array($this,'woo_setup_nav' ));
			add_action( 'bp_init', array($this, 'woo_save_account_details' ) ,999);
			add_action('woocommerce_save_account_details',array($this,'woo_myaccount_page'));
		}

		if ( in_array( 'paid-memberships-pro/paid-memberships-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || (function_exists('is_plugin_active_for_network') && is_plugin_active_for_network( 'paid-memberships-pro/paid-memberships-pro.php'))) {
			add_action( 'bp_setup_nav', array($this,'pmpro_setup_nav' ));
		}

		//Transparent Header
		add_action('wp_head',array($this,'transparent_header_title_background'),99);
    }

    /*
    CSS BACKGROUND WHICH APPLIES WHEN TRANSPARENT HEADER IS ENABLED
     */
    function transparent_header_title_background(){ 
    	$header_style =  vibe_get_customizer('header_style');
    	
    	if($header_style == 'transparent'){ 
	    	if(is_page() || is_single() || bp_is_directory() || is_archive()){ 
	    		global $post;
	    		$title_bg = get_post_meta($post->ID,'vibe_title_bg',true);
	    		if(empty($title_bg)){
	    			$title_bg = vibe_get_option('title_bg');
	    			if(empty($title_bg)){
	    				$title_bg = VIBE_URL.'/assets/images/title_bg.jpg';
	    			}
	    		}
	    		if(is_numeric($title_bg)){
    				$bg = wp_get_attachment_image_src($title_bg,'full');
    				
    				if(!empty($bg))
    					$title_bg = $bg[0];
    			}	
				if(!empty($title_bg)){
	    		?>
	    		<style>.course_header,.group_header{background:url(<?php echo $title_bg; ?>) !important;}#title{background:url(<?php echo $title_bg; ?>) !important;padding-bottom:30px !important; background-size: cover;}#title h1,#title h5,#title a,#title,#breadcrumbs li+li:before{color:#fff !important;}</style>
	    		<?php
	    		}
	    	}
    	}
    }
    function include_child_theme_styling(){
    	if (get_template_directory() !== get_stylesheet_directory()) {
	      	wp_enqueue_style('wplms_child_theme_style',get_stylesheet_uri(),'wplms-style');
	    }
    }

    function site_lock(){
    	$site_lock = vibe_get_option('site_lock');
    	$register_page_id = vibe_get_directory_page('register');
    	$activate_page_id = vibe_get_directory_page('activate');
    	$exlusions = apply_filters('wplms_site_lock_exclusions',array($register_page_id,$activate_page_id));
    	global $post;
    	if(!empty($site_lock) && !is_user_logged_in() && !is_front_page() && !in_Array($post->ID,$exlusions) && (bp_current_component()!='activate')){
    		wp_redirect( home_url() );
        	exit();
    	}
    }
    
	function wplms_removeHeadLinks(){
	  $xmlrpc = vibe_get_option('xmlrpc');
	  if(isset($xmlrpc) && $xmlrpc){
	    remove_action('wp_head', 'rsd_link');
	    remove_action('wp_head', 'wlwmanifest_link'); 
	    add_filter('xmlrpc_enabled','__return_false');
	  }
	}

	function reset_googlewebfonts(){ 
      	echo "reselecting..";
      	$r = get_option('google_webfonts');
      	if(isset($r)){
          	delete_option('google_webfonts');
      	}
	  	die();
	}

	function import_data(){
		if(!current_user_can('manage_options'))
  			die();

		$name = stripslashes($_POST['name']);
		$code = base64_decode(trim($_POST['code'])); 
		if(is_string($code))
    		$code = unserialize ($code);
		
		$value = get_option($name);
		if(isset($value)){
      		update_option($name,$code);
		}else{
			echo "Error, Option does not exist !";
		}
		die();
	}



	function wplms_be_instructor_button(){
		$teacher_form = vibe_get_option('teacher_form');

		if(isset($teacher_form) && is_numeric($teacher_form)){
			echo '<a href="'.(isset($teacher_form)?get_permalink($teacher_form):'#').'" class="button create-group-button full">'. __( 'Become an Instructor', 'vibe' ).'</a>';  
		}
	}

	function course_search_results($query){

	  if(!$query->is_search() && !$query->is_main_query())
	    return $query;

	  if(isset($_GET['course-cat']))
	      $course_cat = $_GET['course-cat'];

	  if(isset($_GET['instructor']))
	      $instructor = $_GET['instructor'];  

	  if ( function_exists('get_coauthors')) {
	    if(isset($instructor) && $instructor !='*' && $instructor !='' && is_numeric($instructor)){
	      $instructor_name = strtolower(get_the_author_meta('user_login',$instructor)); 
	      $query->set('author_name', $instructor_name);
	    }
	  }else{
	    if(isset($instructor) && $instructor !='*' && $instructor !=''){
	      $query->set('author', $instructor);
	    }
	  }

	  if(isset($course_cat) && $course_cat !='*' && $course_cat !=''){
	    $query->set('course-cat', $course_cat);
	  }
	  return $query;
	}


	function vibe_check_access_check(){ 

	    if(!is_singular(array('unit','question')))
	      return;

	    $flag=0;
	    global $post;

		$free=get_post_meta(get_the_ID(),'vibe_free',true);
   		if(vibe_validate($free) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && is_user_logged_in())){
	        	$flag=0;
	        	return;
	    }else
	    	$flag = 1;

	    if(current_user_can('edit_posts')){
	        $flag=0;
	        $instructor_privacy = vibe_get_option('instructor_content_privacy');
	        $user_id=get_current_user_id();
	        if(isset($instructor_privacy) && $instructor_privacy && !current_user_can('manage_options')){
	            if($user_id != $post->post_author)
	              $flag=1;
	        }
	    }

	    if($post->post_type == 'unit'){
	      	$post_type = __('UNITS','vibe');
	    }else if($post->post_type == 'question'){
	      	$post_type = __('QUESTIONS','vibe');
	    }

	    $message = sprintf(__('DIRECT ACCESS TO %s IS NOT ALLOWED','vibe'),$post_type);
	    $flag = apply_filters('wplms_direct_access_to'.$post->post_type,$flag,$post);
	    if($flag){
	        wp_die($message,$message,array('back_link'=>true));
	    }
	}

	
	function vibe_check_course_archive(){

	    if(is_post_type_archive('course') && !is_search()){
	        $pages=get_site_option('bp-pages');
	        if(is_array($pages) && isset($pages['course'])){
	          $all_courses = get_permalink($pages['course']);
	          wp_redirect($all_courses);
	          exit();
	        }
	    }
	}

	// Course functions
	function vibe_custom_print_button(){
		$print_html='<a href="#" class="print_unit"><i class="icon-printer-1"></i></a>';
		echo apply_filters('wplms_unit_print_button',$print_html);  
	}


	function wplms_course_progressbar($course_id,$unit_id){
	    $user_id=get_current_user_id();
	    $course_progressbar = vibe_get_option('course_progressbar');
	    if(!isset($course_progressbar) || !$course_progressbar)
	       return;

	   $key = 'course_progressbar'.$course_id;
	   $percentage = get_user_meta($user_id,$key,true);
	   
	   if(empty($percentage)){
	    $units = bp_course_get_curriculum_units($course_id);
	    $total_units = count($units);
	    $key = array_search($unit_id,$units);
	    $meta=get_user_meta($user_id,$unit_id,true);
	    if(isset($meta) && $meta)
	      $key++;

	    if(!$total_units)$total_units=1;
	    $percentage = round((($key/$total_units)*100),2); // Indexes are less than the count value
	  }
	    
	    if($percentage > 100)
	      $percentage= 100;

	    $unit_increase = round(((1/$total_units)*100),2);

	    echo '<div class="progress course_progressbar" data-increase-unit="'.$unit_increase.'" data-value="'.$percentage.'">
	             <div class="bar animate cssanim stretchRight load" style="width: '.$percentage.'%;"><span>'.$percentage.'%</span></div>
	           </div>';

	}


	function wplms_course_progress_record(){
	    $course_id = $_POST['course_id'];
	    if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'security') || !is_numeric($course_id) ){
	       _e('Security check Failed. Contact Administrator.','vibe');
	       die();
	    }
	    $course_progress = $_POST['progress'];
	    $user_id = get_current_user_id();
	    $progress='progress'.$course_id;
	    update_user_meta($user_id,$progress,$course_progress);
	    die();
	}
	// END course Functions		
	function vibe_product_woocommerce_direct_checkout(){

	  	if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
	        $check=vibe_get_option('direct_checkout');
	        $check =intval($check);
	    	if(isset($check) &&  $check == 2){
	      		if( is_single() && get_post_type() == 'product' && isset($_GET['redirect'])){
	          		global $woocommerce;
	          		$found = false;
	          		$product_id = get_the_ID();
	          		$courses = vibe_sanitize(get_post_meta(get_the_ID(),'vibe_courses',false));
	          		if(isset($courses) && is_array($courses) && count($courses)){
	            		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
	              			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
	                			$_product = $values['data'];
	                			if ( $_product->id == $product_id )
	                  				$found = true;
	              			}
	              			// if product not found, add it
	              			if ( ! $found )
	                			WC()->cart->add_to_cart( $product_id );
	                		$checkout_url = $woocommerce->cart->get_checkout_url();
	                		wp_redirect( $checkout_url);  
        				}else{
	              			// if no products in cart, add it
	              			WC()->cart->add_to_cart( $product_id );
	              			$checkout_url = $woocommerce->cart->get_checkout_url();
	              			wp_redirect( $checkout_url);  
	            		}
	            		exit();
	          		}
	      		}
	    	}
	    	if(isset($check) &&  $check == 3){ 
	      		if( is_single() && get_post_type() == 'product' && isset($_GET['redirect'])){ 
	          		global $woocommerce; 
	          		$found = false;
	          		$product_id = get_the_ID();
	          		$courses = vibe_sanitize(get_post_meta(get_the_ID(),'vibe_courses',false));
	          
	          		if(isset($courses) && is_array($courses) && count($courses)){
	            		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
	              			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
	                		$_product = $values['data'];
	                			if ( $_product->id == $product_id )
	                  				$found = true;
	              			}
	              			// if product not found, add it
	              			if ( ! $found )
	                			WC()->cart->add_to_cart( $product_id );
	                		$cart_url = $woocommerce->cart->get_cart_url(); 
	                		wp_redirect( $cart_url); 
	            		}else{
			              	WC()->cart->add_to_cart( $product_id );
			              	$cart_url = $woocommerce->cart->get_cart_url(); 
			              	wp_redirect( $cart_url);
	            		}
	            		exit();
	          		}
	      		}
	    	}
	  	} // End if WooCommerce Installed
	}

	function vibe_view_woocommerce_order_course_details($html, $item ){
		 
	  	$product_id=$item['item_meta']['_product_id'][0];
	  	if(isset($product_id) && is_numeric($product_id)){
	      	$courses = get_post_meta($product_id,'vibe_courses',true);
	      	if(!empty($courses) && is_Array($courses)){
		        $html .= ' [ <i>'.__('COURSE : ','vibe');
	        	foreach($courses as $course){ 
	          		if(is_numeric($course)){ 
	           			$html .= '<a href="'.get_permalink($course).'"><strong><i>'.get_post_field('post_title',$course).'</i></strong></a> ';
	          		}
	        	}
	        	$html .=' </i> ]';
	      	}
	  	}
	  	return $html;

	}
	
	function wplms_social_buttons_on_product(){
	    echo do_shortcode('[social_buttons]');
	}


	function vibe_redirect_after_registration($user_id, $key, $user){
		
		$bp = buddypress();
		
		$bp->activation_complete = true;

		if(current_user_can('manage_options'))
			return;


	    if ( is_multisite() )
	      $hashed_key = wp_hash( $key );
	    else
	      $hashed_key = wp_hash( $user_id );

	    if ( file_exists( BP_AVATAR_UPLOAD_PATH . '/avatars/signups/' . $hashed_key ) )
	      @rename( BP_AVATAR_UPLOAD_PATH . '/avatars/signups/' . $hashed_key, BP_AVATAR_UPLOAD_PATH . '/avatars/' . $user_id );

	     
	    
	    $pageid=vibe_get_option('activation_redirect');
	    if(empty($pageid)){
	      bp_core_add_message( __( 'Your account is now active!', 'vibe' ) );
	      bp_core_redirect( apply_filters ( 'wplms_registeration_redirect_url', bp_core_get_user_domain( $user_id ), $user_id ) );      
	    }else{
	    	wp_set_auth_cookie( $user_id, true, false );	
	      	$link = get_permalink($pageid);
	      	bp_core_redirect( apply_filters ( 'wplms_registeration_redirect_url',$link, $user_id ) );      
	    }
	}

	/*=== Layout 3 ===*/
	function member_layout_3_before_item_tabs(){
		$layout = vibe_get_customizer('profile_layout');
		if($layout != 'p3')
			return;
		?>
			<div class="row">
				<div class="col-md-3">
		<?php
	}

	function member_layout_3_after_item_tabs(){
		$layout = vibe_get_customizer('profile_layout');
		if($layout != 'p3')
			return;
		?>
			</div>
			<div class="col-md-9">
		<?php
	}

	function member_layout_3_end_body(){
		$layout = vibe_get_customizer('profile_layout');
		if($layout != 'p3')
			return;
		?>
			</div>
		</div>
		<?php
	}

	function group_layout_3_before_item_tabs(){
		$layout = vibe_get_customizer('group_layout');
		if($layout != 'g3')
			return;
		?>
			<div class="row">
				<div class="col-md-3">
		<?php
	}
	function group_layout_3_after_item_tabs(){
		$layout = vibe_get_customizer('group_layout');
		if($layout != 'g3')
			return;
		?>
			</div>
			<div class="col-md-9">
		<?php
	}

	function group_layout_3_end_body(){
		$layout = vibe_get_customizer('profile_layout');
		if($layout != 'p3')
			return;
		?>
			</div>
		</div>
		<?php
	}


	function woo_setup_nav(){
		global $bp;
		$myaccount_pid = get_option('woocommerce_myaccount_page_id');

		if(is_numeric($myaccount_pid)){
			$slug = get_post_field('post_name',$myaccount_pid);
			bp_core_new_nav_item( array( 
	            'name' => __('My Orders', 'vibe' ), 
	            'slug' => $slug , 
	            'position' => 99,
	            'screen_function' => array($this,'woo_myaccount'), 
	            'default_subnav_slug' => '',
	            'show_for_displayed_user' => bp_is_my_profile(),
	            'default_subnav_slug'=> $slug
	      	) );


			$link = trailingslashit( bp_loggedin_user_domain() . $slug );

			bp_core_new_subnav_item( array(
				'name'            => __('My Orders', 'vibe' ), 
				'slug'            => $slug,
				'parent_slug'     => $slug,
				'parent_url'      => $link,
				'position'        => 10,
				'item_css_id'     => 'nav-' . $slug,
				'screen_function' => array( $this, 'woo_myaccount' ),
				'user_has_access' => bp_is_my_profile(),
				'no_access_url'   => home_url(),
			) );
			
			$endpoints = array(
				'edit-account' => get_option( 'woocommerce_myaccount_edit_account_endpoint', 'edit-account' ),
			);

			$i=20;
			foreach($endpoints as $key => $endpoint){
				switch ( $endpoint ) {
					case 'edit-account' :
						$title = __( 'Edit Account Details', 'vibe' );
					break;
					default :
						$title = __( 'My Orders', 'vibe' );
					break;
				}
				$function = str_replace('-','_',$key);
				
				bp_core_new_subnav_item( array(
					'name'            => $title,
					'slug'            => $key,
					'parent_slug'     => $slug,
					'parent_url'      => $link,
					'position'        => $i,
					'item_css_id'     => 'nav-' . $key,
					'screen_function' => array( $this, $function ),
					'user_has_access' => bp_is_my_profile(),
					'no_access_url'   => home_url(),
				) );
				$i = $i+10;
			}
		}
	}
	function woo_myaccount() {

		if(!is_user_logged_in() || !function_exists('bp_is_my_profile') || !bp_is_my_profile())
			wp_redirect(home_url());

		$this->myaccount_pid = get_option('woocommerce_myaccount_page_id');
		add_action('bp_template_title',array($this,'woo_myaccount_title'));
		add_action('bp_template_content',array($this,'woo_myaccount_content'));
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );		
		exit;
	}
	
	function edit_account(){
		if(!is_user_logged_in() || !function_exists('bp_is_my_profile') || !bp_is_my_profile())
			wp_redirect(home_url());

		add_query_arg($bp->current_action);
		
		if(empty($this->myaccount_pid))
			$this->myaccount_pid = get_option('woocommerce_myaccount_page_id');


		add_action('bp_template_title',array($this,'woo_myaccount_edit_title'));
		add_action('bp_template_content',array($this,'woo_myaccount_edit_content'));
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		exit;
	}

	function woo_myaccount_title(){
		echo '<h2>'.get_the_title($this->myaccount_pid).'</h2>';
	}

	function woo_myaccount_edit_title(){
		echo '<h2>'.__( 'Edit Account Details', 'vibe' ).'</h2>';
	}

	function woo_myaccount_content(){
		echo apply_filters('the_content',get_post_field('post_content',$this->myaccount_pid));
	}

	function woo_myaccount_edit_content(){
		ob_start();
		wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) );
		$content = ob_get_clean();
		echo apply_filters('the_content',$content);
	}
	function woo_save_account_details(){
		if(isset($_POST)){
			WC_Form_Handler::save_account_details();
		}
	}

	function woo_myaccount_page(){
		$myaccount_pid = get_option('woocommerce_myaccount_page_id');
		if(is_numeric($myaccount_pid)){
			$slug = get_post_field('post_name',$myaccount_pid);
			$link = trailingslashit( bp_loggedin_user_domain() . $slug );
			wp_redirect($link);
			exit();
		}
	}

	/* === PMPRO ===== */
	function pmpro_setup_nav(){
		global $bp;
		if(empty($this->pmpro_account_pid))
			$this->pmpro_account_pid = get_option('pmpro_account_page_id');

		if(is_numeric($this->pmpro_account_pid)){
			$slug = get_post_field('post_name',$this->pmpro_account_pid);
			bp_core_new_nav_item( array( 
	            'name' => __('My Memberships', 'vibe' ), 
	            'slug' => $slug , 
	            'position' => 99,
	            'screen_function' => array($this,'pmpro_myaccount'), 
	            'default_subnav_slug' => '',
	            'show_for_displayed_user' => bp_is_my_profile(),
	            'default_subnav_slug'=> $slug
	      	) );


			$link = trailingslashit( bp_loggedin_user_domain() . $slug );

			bp_core_new_subnav_item( array(
				'name'            => __('My Memberships', 'vibe' ), 
				'slug'            => $slug,
				'parent_slug'     => $slug,
				'parent_url'      => $link,
				'position'        => 10,
				'item_css_id'     => 'nav-' . $slug,
				'screen_function' => array( $this, 'pmpro_myaccount' ),
				'user_has_access' => bp_is_my_profile(),
				'no_access_url'   => home_url(),
			) );
		}
	}
	function pmpro_myaccount() {

		if(!is_user_logged_in() || !function_exists('bp_is_my_profile') || !bp_is_my_profile())
			wp_redirect(home_url());
		
		if(empty($this->pmpro_account_pid))
			$this->pmpro_account_pid = get_option('pmpro_account_page_id');

		add_action('bp_template_title',array($this,'pmpro_myaccount_title'));
		add_action('bp_template_content',array($this,'pmpro_myaccount_content'));
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );		
		exit;
	}

	function pmpro_myaccount_title(){
		echo '<h2>'.get_the_title($this->pmpro_account_pid).'</h2>';
	}

	function pmpro_myaccount_content(){
		echo apply_filters('the_content',get_post_field('post_content',$this->pmpro_account_pid));
	}

	function forgot_password(){
    	if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'security')){
	       _e('Security check Failed. Contact Administrator.','vibe');
	       die();
	    }

	    $fields = json_decode(stripslashes($_POST['fields']));
	    
	    if(!empty($_POST['user_login'])){
	    	$user = get_user_by( 'email', $_POST['user_login']);
	    	if(is_wp_error($user)){
	    		$error_string = $message->get_error_message();
    			echo '<div class="message error">' . $error_string . '</div>';
	    	}else{
	    		$message = vibe_projects_retrieve_password();
	    		if(is_wp_error($message)){
	    			$error_string = $message->get_error_message();
	    			echo '<div class="message error">' . $error_string . '</div>';
	    		}else{
	    			echo '<div class="message success">'.__('Password reset link has been sent to your email.','vibe').'</div>';
	    		}
	    	}
	    }else{
	    	echo '<div class="message error">'.__('Email address is required for password reset.','vibe').'</div>';
	    }

	    die();
    } 
}

WPLMS_Actions::init();