<?php
/**
 * Installation related functions and actions.
 *
 * @author 		VibeThemes
 * @category 	Admin
 * @package 	Vibe Projects/Includes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class bp_profile_cover_functions{

	var $message;

	function __construct(){
		add_action( 'bp_xprofile_setup_nav', array($this,'bp_profile_cover_setup_nav'));
		add_action('bp_before_group_header',array($this,'bp_group_profile_cover'));
		add_action('bp_before_member_home_content',array($this,'display_cover'));
		add_action('bp_before_member_plugin_template',array($this,'display_cover'));
	}

	function bp_profile_cover_setup_nav() {
	    global $bp;
	    $profile_link = bp_loggedin_user_domain() . $bp->profile->slug . '/';
	    bp_core_new_subnav_item(
	        array(
	            'name' => __('Change Cover', 'bp-profile-cover'),
	            'slug' => 'change-cover',
	            'parent_url' => $profile_link,
	            'parent_slug' => $bp->profile->slug,
	            'screen_function' => array($this,'bp_profile_cover_upload'),
	            'user_has_access' => (bp_is_my_profile() || is_super_admin()),
	            'position' => 40
	        )
	    );

	}
	function bp_profile_cover_upload() {
		global $bp;

		if(isset($_POST['action']) && $_POST['action'] == 'bp_cover_upload' ){ 

			if ( !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'],'bp_cover_upload') ){
			    $this->message = '<div class="error message">'.__('Security check Failed. Contact Administrator.','bp-profile-cover' ).'</div>';
			}else{
				if(! empty( $_FILES['profile_cover']['name'])){
					$attachment = new BP_Profile_Cover();
					$file = $attachment->upload( $_FILES );
					if ( ! empty( $file['error'] ) ) {
						$this->message =  '<div class="error message">'.$file['error'].'</div>';
					} else{
						update_user_meta($bp->loggedin_user->id,'cover',$file['url']);
						$this->message =  '<div class="success message">'.__('Cover image uploaded successfully','bp-profile-cover' ).'</div>';
					}
				}else if(isset($_POST['delete_profile_cover'])){
					delete_user_meta($bp->loggedin_user->id,'cover');
				}
			}
			
		}
	    
	    add_action('bp_template_content',array($this,  'bp_profile_cover_page_content'));
	    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
	}

	//Upload page content
	function bp_profile_cover_page_content() {
		echo $this->message;
		$user_id = get_current_user_id();
		if(isset($_POST['position_x'])){ 
			if(wp_verify_nonce($_POST['_wpnonce'],'bp_cover_upload')){ 
				update_user_meta($user_id,'position_x',$_POST['position_x']);
			}
		}
		if(isset($_POST['position_y'])){ 
			if(wp_verify_nonce($_POST['_wpnonce'],'bp_cover_upload')){ 
				update_user_meta($user_id,'position_y',$_POST['position_y']);
			}
		}
		if(!empty($_POST['cover_repeat'])){
			if(wp_verify_nonce($_POST['_wpnonce'],'bp_cover_upload')){
				update_user_meta($user_id,'repeat',$_POST['cover_repeat']);
			}
		}
		?>
		<h4><?php _e( 'Add/Edit profile cover photo','bp-profile-cover' ); ?></h4>
		<?php do_action( 'bp_before_profile__upload_content' ); ?>

			<p><?php _e( 'You can upload an image from your computer.', 'bp-profile-cover'); ?></p>

			<form action="" method="post" id="cover-upload-form" class="standard-form" enctype="multipart/form-data">
				<?php wp_nonce_field( 'bp_cover_upload' ); ?>
					<p><?php _e( 'Click below to select a JPG or PNG format photo from your computer and then click \'Upload Image\' to proceed.','bp-profile-cover' ); ?></p>

					<p id="cover-upload"><br />
						<input type="file" name="profile_cover" id="profile_cover" /><br />
						<input type="hidden" name="action" id="action" value="bp_cover_upload" />
						
						<?php 
								$position_x = get_user_meta($user_id,'position_x',true);
								$position_y = get_user_meta($user_id,'position_y',true);
       							$repeat = get_user_meta($user_id,'repeat',true);
						?>
						<input type="number" name="position_x" style="width:80px;" value="<?php echo (isset($position_x)?$position_x:'50%'); ?>" placeholder="<?php esc_attr_e( 'X Postion %','bp-profile-cover' ); ?>" />
						<input type="number" name="position_y" style="width:80px;" value="<?php echo (isset($position_y)?$position_y:'50%'); ?>" placeholder="<?php esc_attr_e( 'Y Postion %','bp-profile-cover' ); ?>" /><br />
						<br />
						<label><input type="radio" name="cover_repeat" value="repeat" <?php checked($repeat,'repeat'); ?>/> Повторять</label>&nbsp;&nbsp;
						<label><input type="radio" name="cover_repeat" value="no-repeat" <?php checked($repeat,'no-repeat'); ?>/> Не повторять</label><br /><br />
						<input type="submit" name="profile_cover" id="upload" value="<?php esc_attr_e( 'Upload Image & Save Settings','bp-profile-cover' ); ?>" />
					</p><br />
					
					<?php if ( bp_get_user_has_cover() ) : ?>
						<p><?php _e( "If you'd like to delete your current cover but not upload a new one, please use the delete cover button.",'bp-profile-cover' ); ?></p>
						<input type="submit" name="delete_profile_cover" id="delete" value="<?php esc_attr_e( 'Delete Cover','bp-profile-cover' ); ?>" />
					<?php endif; ?>

			</form>
		<?php do_action( 'bp_after_profile_cover_upload_content' ); 
		
	}

	function display_cover(){ 
       $user_id = bp_displayed_user_id();
       $cover_url = get_user_meta($user_id,'cover',true);
       $position_x = intval(get_user_meta($user_id,'position_x',true));
       $position_y = intval(get_user_meta($user_id,'position_y',true));
       $repeat = get_user_meta($user_id,'repeat',true);
       if(!isset($position_x)){
       	 $position_x = '50';
       }
       if(!isset($position_y)){
       	 $position_y = '50';
       }
       if(empty($repeat)){
       	 $repeat = 'no-repeat';
       }
       $default = BP_Profile_Cover_Settings::init()->get();
       if(empty($cover_url) ){
       		if(empty($default) || empty($default['default_image'])){
       			$cover_url = apply_filters('bp_profile_cover_default',plugin_dir_url( __FILE__ ).'default_cover.jpeg');
       		}else{
       			$attachment = wp_get_attachment_image_src($default['default_image'],'full');
       			if(empty($attachment) || empty($attachment[0])){
       				$cover_url = apply_filters('bp_profile_cover_default',plugin_dir_url( __FILE__ ).'default_cover.jpeg');
       			}else{
       				$cover_url = $attachment[0];	
       			}
       			
       		}           
       }


       echo '<style>.bp-user #buddypress #item-header{
       	padding:20px;
       	background:url("'.$cover_url.'") '.$position_x.'% '.$position_y.'% '.$repeat.' '.(empty($default['background_attachment'])?'fixed':$default['background_attachment']).' !important; 
       	-webkit-background-size: '.(empty($default['background_size'])?'cover':$default['background_size']).' !important;
    	background-size: '.(empty($default['background_size'])?'cover':$default['background_size']).' !important;
    	}</style>';
    }
	function bp_group_profile_cover(){ 
		global $bp; 
		$group_id = $bp->groups->current_group->id;
		$cover_url = groups_get_groupmeta(  $group_id, 'cover' );
		$position_x = intval(groups_get_groupmeta( $group_id,'position_x'));
		$position_y = intval(groups_get_groupmeta( $group_id,'position_y'));
       	$repeat = groups_get_groupmeta( $group_id,'repeat');
       	if(!isset($position_x)){
       	 $position_x = '50';
       }
       if(!isset($position_y)){
       	 $position_y = '50';
       }
        if(empty($repeat)){
	       	 $repeat = 'no-repeat';
       	}
        if(empty($cover_url) ){
       		$default = BP_Profile_Cover_Settings::get();
       		if(empty($default) || empty($default['default_image'])){
       			$cover_url = apply_filters('bp_profile_cover_default',plugin_dir_url( __FILE__ ).'default_cover.jpeg');
       		}else{
       			$attachment = wp_get_attachment_image_src($default['default_image'],'full');
       			if(empty($attachment) || empty($attachment[0])){
       				$cover_url = apply_filters('bp_profile_cover_default',plugin_dir_url( __FILE__ ).'default_cover.jpeg');
       			}else{
       				$cover_url = $attachment[0];	
       			}
       			
       		}           
        }
        echo '<style> #item-header{
        	padding:20px;    
        	background:url("'.$cover_url.'") '.$position_x.'% '.$position_y.'% '.$repeat.' '.(empty($default['background_attachment'])?'fixed':$default['background_attachment']).' !important; 
       		-webkit-background-size: '.(empty($default['background_size'])?'cover':$default['background_size']).' !important;
    		background-size: '.(empty($default['background_size'])?'cover':$default['background_size']).' !important;
        } </style>';
	}
}

new bp_profile_cover_functions;



function bp_get_user_has_cover(){
	global $bp;
	$check = get_user_meta($bp->loggedin_user->id,'cover',true);
	if(!empty($check)){
		return true;
	}else{
		return false;
	}
}

function bp_get_group_has_cover(){
	return 0;
}


