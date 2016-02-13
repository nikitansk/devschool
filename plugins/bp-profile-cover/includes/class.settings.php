<?php
/**
 * Setup BP Profile Cover settings
 *
 * @class       BP_Profile_Cover_Settings
 * @author      VibeThemes
 * @category    Admin
 * @package     BP Profile Cover
 * @version     1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class BP_Profile_Cover_Settings{

	var $settings;
	var $temp;

	public static $instance;
	
	public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new BP_Profile_Cover_Settings();
        return self::$instance;
    }

	private function __construct(){
		$this->option = 'bp-profile-cover';
		add_options_page(__('BP Profile Coversettings','bp-profile-cover'),__('BP Profile Cover','bp-profile-cover'),'manage_options','bp-profile-cover',array($this,'settings'));
		$this->settings=$this->get(); 
	}

	function settings(){
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
		$this->settings_tabs($tab);
		$this->$tab();
	}

	function settings_tabs( $current = 'general' ) {
		$tabs = array( 
	    		'general' => __('General','bp-profile-cover'), 
	    		);
	    echo '<div id="icon-themes" class="icon32"><br></div>';
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
	        echo "<a class='nav-tab$class' href='?page=bp-profile-cover&tab=$tab'>$name</a>";

	    }
	    echo '</h2>';
	    if(isset($_POST['save'])){
	    	$this->save();
	    }
	}

	function general(){
		echo '<h3>'.__('General Settings','bp-profile-cover').'</h3>';
	
		$settings=array(
				array(
					'label' => __('Default Image','bp-profile-cover' ),
					'name' =>'default_image',
					'type' => 'upload',
					'button'=> __('Upload Default Image','bp-profile-cover' ),
					'desc' => __('Set Login redirect settings','bp-profile-cover' )
				),
				array(
					'label' => __('Background Size','bp-profile-cover' ),
					'name' =>'background_size',
					'type' => 'select',
					'options'=> array(
						'auto' => __('auto','bp-profile-cover' ),
						'cover' => __('Cover','bp-profile-cover' ),
						'contain' => __('Contain','bp-profile-cover' ),
						),
					'desc' => __('Set default cover background size ','bp-profile-cover' ).' <a href="http://www.w3schools.com/cssref/css3_pr_background-size.asp" target="_blank">'.__('Learn more','bp-profile-cover').'</a>'
				),
				array(
					'label' => __('Background Attachment','bp-profile-cover' ),
					'name' =>'background_attachment',
					'type' => 'select',
					'options'=> array(
						'scroll' => __('Scroll','bp-profile-cover' ),
						'fixed' => __('Fixed','bp-profile-cover' ),
						'inherit' => __('inherit','bp-profile-cover' ),
						),
					'desc' => __('Set default cover background attachment ','bp-profile-cover' ).' <a href="http://www.w3schools.com/cssref/pr_background-attachment.asp" target="_blank">'.__('Learn more','bp-profile-cover').'</a>'
				),
			);

		$this->generate_form('general',$settings);
	}
	function generate_form($tab,$settings=array()){
		echo '<form method="post">
				<table class="form-table">';
		wp_nonce_field('save_settings','_wpnonce');   
		echo '<ul class="save-settings">';

		foreach($settings as $setting ){
			echo '<tr valign="top">';
			global $wpdb,$bp;
			switch($setting['type']){
				case 'textarea': 
					echo '<th scope="row" class="titledesc">'.$setting['label'].'</th>';
					echo '<td class="forminp"><textarea name="'.$setting['name'].'">'.(isset($this->settings[$setting['name']])?$this->settings[$setting['name']]:(isset($setting['std'])?$setting['std']:'')).'</textarea>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'select':
					echo '<th scope="row" class="titledesc">'.$setting['label'].'</th>';
					echo '<td class="forminp"><select name="'.$setting['name'].'" class="chzn-select">';
					foreach($setting['options'] as $key=>$option){
						echo '<option value="'.$key.'" '.(isset($this->settings[$setting['name']])?selected($key,$this->settings[$setting['name']]):'').'>'.$option.'</option>';
					}
					echo '</select>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'checkbox':
					echo '<th scope="row" class="titledesc">'.$setting['label'].'</th>';
					echo '<td class="forminp"><input type="checkbox" name="'.$setting['name'].'" '.(isset($this->settings[$setting['name']])?'CHECKED':'').' />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'number':
					echo '<th scope="row" class="titledesc">'.$setting['label'].'</th>';
					echo '<td class="forminp"><input type="number" name="'.$setting['name'].'" value="'.(isset($this->settings[$setting['name']])?$this->settings[$setting['name']]:'').'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'hidden':
					echo '<input type="hidden" name="'.$setting['name'].'" value="1"/>';
				break;
				case 'upload':
					echo '<th scope="row" class="titledesc">'.$setting['label'].'</th>';
					echo '<td class="forminp">';
					echo '<div class="upload_button">';

					wp_enqueue_media();

					$setting['value'] =$this->settings[$setting['name']];
					if(is_numeric($setting['value']))	{
						$attachment = wp_get_attachment_image_src($setting['value']);
						if(empty($attachment)){
							echo '<a id="'.$setting['name'].'" data-input-name="'.$setting['name'].'" data-uploader-title="'.$setting['label'].'" data-uploader-button-text="'.$setting['button'].'"><span class="dashicons dashicons-format-image"></span></a>';
						}else{
							$url = $attachment[0];	
							echo '<a id="'.$setting['name'].'" data-input-name="'.$setting['name'].'" data-uploader-title="'.$setting['label'].'" data-uploader-button-text="'.$setting['button'].'"><img src="'.$url.'" class="submission_thumb thumbnail" /><input type="hidden" value="'.$setting['value'].'" /></a>';
						}
					}else{
						echo '<a id="'.$setting['name'].'" data-input-name="'.$setting['name'].'" data-uploader-title="'.$setting['label'].'" data-uploader-button-text="'.$setting['button'].'"><span class="dashicons dashicons-format-image"></span></a>';
					}
					echo '<style>.upload_button img{width:200px;border-radius:2px;}.upload_button>a>span{    width: 60px;overflow: hidden;border-radius: 2px;display: inline-block;text-align: center;padding: 20px 0;font-size: 20px;color: #888;border: 2px solid #ccc;}</style><script>
					var media_uploader'.$setting['name'].';
					jQuery("#'.$setting['name'].'").on("click", function( event ){
					  
					    var button = jQuery( this );
					    if ( media_uploader'.$setting['name'].' ) {
					      media_uploader'.$setting['name'].'.open();
					      return;
					    }
					    // Create the media uploader.
					    media_uploader'.$setting['name'].' = wp.media.frames.media_uploader'.$setting['name'].' = wp.media({
					        title: button.attr( "data-uploader-title"),
					        library: {
					            type: "image",
					            query: false
					        },
					        button: {
					            text: button.attr("data-uploader-button-text"),
					        },
					    });

					    // Create a callback when the uploader is called
					    media_uploader'.$setting['name'].'.on( "select", function() {
					        var selection = media_uploader'.$setting['name'].'.state().get("selection"),
					            input_name = button.data( "input-name");
					            selection.map( function( attachment ) {
					            	attachment = attachment.toJSON(); console.log(attachment);
					            	button.html("<img src=\'"+attachment.url+"\' class=\'submission_thumb thumbnail\' /><input id=\'"+input_name +"\' class=\'form-control post_field\' name=\'"+input_name+"\' data-id=\''.$setting['name'].'\' type=\'hidden\' value=\'"+attachment.id+"\' />");
					         	});
					    });
					    // Open the uploader
					    media_uploader'.$setting['name'].'.open();
					  });
				</script>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				default:
					echo '<th scope="row" class="titledesc">'.$setting['label'].'</th>';
					echo '<td class="forminp"><input type="text" name="'.$setting['name'].'" value="'.(isset($this->settings[$setting['name']])?$this->settings[$setting['name']]:(isset($setting['std'])?$setting['std']:'')).'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
			}
			
			echo '</tr>';
		}
		echo '</tbody>
		</table>';
		echo '<input type="submit" name="save" value="'.__('Save Settings','bp-profile-cover').'" class="button button-primary" /></form>';
	}

	function get(){
		return get_option($this->option);
	}

	function put($value){
		update_option($this->option,$value);
	}

	function save(){
		$none = $_POST['save_settings'];
		if ( !isset($_POST['save']) || !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'],'save_settings') ){
		    _e('Security check Failed. Contact Administrator.','bp-profile-cover');
		    die();
		}
		unset($_POST['_wpnonce']);
		unset($_POST['_wp_http_referer']);
		unset($_POST['save']);

		foreach($_POST as $key => $value){
			$this->settings[$key]=$value;
		}

		$this->put($this->settings);
	}
}

add_action('admin_menu','init_bp_profile_cover_settings_settings',100);
function init_bp_profile_cover_settings_settings(){
	BP_Profile_Cover_Settings::init();
}
