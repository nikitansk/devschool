<?php
/**
 * EventON Admin Functions
 *
 * Hooked-in functions for EventON related events in admin.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin
 * @version     1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Prevent non-admin access to backend */
	function eventon_prevent_admin_access() {
		if ( get_option('eventon_lock_down_admin') == 'yes' && ! is_ajax() && ! ( current_user_can('edit_posts') || current_user_can('manage_eventon') ) ) {
			//wp_safe_redirect(get_permalink(woocommerce_get_page_id('myaccount')));
			exit;
		}
	}

/* eventON shortcode generator button for WYSIWYG editor */
//add_action('init', 'eventon_shortcode_button_init');
	 function eventon_shortcode_button_init() {

	 	global $pagenow, $typenow, $post;	
		
		if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $post = get_post( $_GET['post'] );
	        $typenow = (!empty($post) )? $post->post_type : '';
	    }
		
		if ( $typenow == '' || $typenow == "ajde_events" ) return;
		

	      //Abort early if the user will never see TinyMCE
	      if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
	           return;

	      //Add a callback to regiser our tinymce plugin   
	      add_filter("mce_external_plugins", "eventon_register_tinymce_plugin"); 

	      // Add a callback to add our button to the TinyMCE toolbar
	      add_filter('mce_buttons', 'eventon_add_tinymce_button');
	}
	//This callback registers our plug-in
	function eventon_register_tinymce_plugin($plugin_array) {
	    $plugin_array['eventon_shortcode_button'] = AJDE_EVCAL_URL.'/assets/js/admin/shortcode.js';
	    return $plugin_array;
	}

	//This callback adds our button to the toolbar
	function eventon_add_tinymce_button($buttons) {
	            //Add the button ID to the $button array
	    $buttons[] = "eventon_shortcode_button";
	    return $buttons;
	}

// @since 2.2.24
// check if repeat post data are good to go
	function eventon_is_good_repeat_data(){
		return ( isset($_POST['evcal_rep_freq'])
			&& isset($_POST['evcal_repeat']) 
			&& $_POST['evcal_repeat']=='yes')? 	true: false;
	}


// SAVE: closed meta field boxes
	function eventon_save_collapse_metaboxes( $page, $post_value ) {
		
		if(empty($post_value)) return;
		
		$user_id = get_current_user_id();
		$option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
		
		$meta_box_ids = array_unique(array_filter(explode(',',$post_value)));
		
		$meta_box_id_ar =serialize($meta_box_ids);
		
		update_user_option( $user_id, $option_name,  $meta_box_id_ar , true );
		
	}

	function eventon_get_collapse_metaboxes($page){
		
		$user_id = get_current_user_id();
	    $option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
		$option_arr = get_user_option( $option_name, $user_id );
		
		if(empty($option_arr)) return;
		
		return unserialize($option_arr);
		//return ($option_arr);		
	}

// added in version 2.2.21
	// check if curl installed
		function evo_curl_installed(){
			return (function_exists('curl_exec'))? true: false;
		}
	// get file content using curl
		function evo_file_get_contents_curl($url) {
		    $ch = curl_init();

		    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

		    $data = curl_exec($ch);
		    curl_close($ch);

		    return $data;
		}
	// test wp_remote_post() call to json.php file
		function evo_wp_remote_test($protocol){
			if($protocol=='post'){
				$request_string = array(
					'body' => array(
						'action' => 'wp_remote_post', 
					),
				);		
			
				$request = wp_remote_post('http://74.62.111.12/~updates/eventon/json.php', $request_string);
				if(!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200 ){
					if(isset($request['response']) && $request['response']['code']== 200 && $request['body']=='wp_remote_post'){
						save_evoOPT('1', 'wp_remote_post','worked');
						return true;
					}else{
						save_evoOPT('1', 'wp_remote_post','didnt_work');
						return false;
					}					
				}else{	save_evoOPT('1', 'wp_remote_post','didnt_work'); return false;	}
			}else{// GET
				$url = 'http://74.62.111.12/~updates/eventon/json.php';
				$request = wp_remote_get($url);
				if (!is_wp_error($request) && $request['response']['code']===200) { 
					save_evoOPT('1', 'wp_remote_get','worked');
					return (!empty($request['body']) && $request['body']=='other_content')? true: false;
				}else{	
					save_evoOPT('1', 'wp_remote_get','didnt_work');
					return false;	}

			}
			
		}

	// license activation debug report
		function evo_debug_report(){

			$admin_url = admin_url();
			echo "<p><a class='evo_admin_btn btn_prime' href='".$admin_url."admin.php?page=eventon&tab=evcal_4&action=debug'>Run debug report</a></p>";
			// run debug
			if(isset($_GET['action']) && $_GET['action']=='debug'):
				
			

			$msg_ = "<br/><br/>EventON will resolve to backup method to activate eventON license locally.<br/><br/>This mean, eventON addons can not be activate from your site until wp_remote_post() access is resolved. BUT once eventON is activated you should be able to use addons to their full functionality. <br/><br/>Learn <a target='_blank' href='http://www.myeventon.com/documentation/can-download-addon-updates/'>How to download addon updates</a>";

			$msg1 = "<br/>cURL looks to be installed in your server. Please contact your webhost and check to make sure they do not have any remote access restrictions.".$msg_;
			$msg2 = "<br/>cURL is not enabled in your server. Please contact your webhost and make sure curl is enabled in php.ini".$msg_;
			$msg3 = "wp_remote_post() works fine on your site. You should be able to activate eventON products.";
			$msg4 = "wp_remote_post() did NOT work on your site but wp_remote_get() works fine on your site. You should be able to activate eventON products.";



			ob_start();
			?>
			<p style='display:block; padding:0 5px 10px; font-style:italic'><b>DEBUG REPORT:</b> <br/>
			<?php 

				$code_process = 00;
				
				// check if wp_remote_already checked and status saved
				$wprem = get_evoOPT('1','wp_remote_post');
				$wpremG = get_evoOPT('1','wp_remote_get');
				
				// check if post works still
				$wp_remote_test = evo_wp_remote_test('post');
				if($wp_remote_test){ // post worked
					$code_process.= ' 1';
					$backupmethod = false;
					echo $msg3;
				}else{// try wp_remote_get
					$code_process.= ' 2';
					$backupmethod = true;						
				}

				// post not working backup methods
				if($backupmethod==true){
					if($wpremG=='worked'){ // get worked
						$code_process.= ' 3';
						echo $msg4;
					}else{ // GET didnt work Or empty
						$wp_remote_testG = evo_wp_remote_test('get'); // re-test get
						if($wp_remote_testG){// get work now
							$code_process.= ' 4';
							echo $msg4;
						}else{// get doesnt work still -- resolve to curl
							$curl = evo_curl_installed();
							echo "wp_remote_post() or wp_remote_get() methods did NOT work on your site.";
							if($curl){// curl check
								$code_process.= ' 5';
								echo $msg1;
							}else{// no luck buddy
								$code_process.= ' 6';
								echo $msg2;
							}
						}
					}
				}

				//echo $code_process;

			?></p>

			<?php 
			endif;

			return ob_get_clean();
		}

	function eventon_hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb[0].','.$rgb[1].','.$rgb[0]; // returns an array with the rgb values
	}



?>