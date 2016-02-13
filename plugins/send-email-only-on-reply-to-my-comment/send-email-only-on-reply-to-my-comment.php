<?php
/*
Plugin Name: Send email only on Reply to My Comment
Version: 1.0.6
Plugin URI: http://elance360.com/wordpress-plugin/
Description: This plugin gives your site users the option to receive email notifications Only When someone selects to reply to this person's Comment.
Author: Yasir
Author URI: http://elance360.com/
*/

// Avoid direct access to this piece of code
if (!function_exists('add_action')){
	header('Location: /');
	exit;
}

function run_at_activation(){
	    add_option('subscribe_reloaded_dropdown_label', __("Do Not Send Email Notifications.",'subscribe-reloaded'), '', 'no');
        add_option('subscribe_reloaded_dropdown_label1', __("Send Email Notification ONLY If Someone Replies To My Comment(s).",'subscribe-reloaded'), '', 'no');
        add_option('subscribe_reloaded_dropdown_label2', __("Send Email Notification Whenever A New Comment Is Posted.",'subscribe-reloaded'), '', 'no');
        add_option('subscribe_reloaded_show_subscription_box1', 'yes', '', 'no');
		add_option('subscribe_reloaded_show_subscription_box', 'yes', '', 'no');
		add_option('subscribe_reloaded_sub_options', 'op1', '', 'no');
	global $wpdb;
	$checkalready_first_comment = $wpdb->get_col("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key` = '_stt@_notsend_dup'");
	if(!($checkalready_first_comment)){
		global $wpdb;
		$get_last_comment =	$wpdb->get_var("SELECT `comment_ID` FROM `wp_comments` WHERE `comment_approved` = '1' ORDER BY `comment_ID` DESC");
		if ($get_last_comment != null){
		$a_p = "1";
		$a_K = "_stt@_notsend_dup";
		global $wpdb;
		$wpdb->insert('wp_postmeta', array( 
		'post_id' => $a_p, 
		'meta_key' => $a_K,
		'meta_value' => $get_last_comment), 
	array( 
		'%s', 
		'%s',
		'%s') ); }}
	 if (get_option('subscribe_reloaded_enable_advanced_subscriptions', 'yes') == 'no'){
	 global $wpdb;
	 $wpdb->update( 'wp_options', 
	array( 'option_value' => 'yes'), 
	array( 'option_name' => 'subscribe_reloaded_enable_advanced_subscriptions' ), 
	array( '%s'));}
			if (get_option('subscribe_reloaded_checked_by_default', '1') == 'no'){
			global $wpdb;
			$wpdb->update('wp_options',	array('option_value' => '1'), array( 'option_name' => 'subscribe_reloaded_checked_by_default' ), array('%s'));
            global $wpdb;
			$wpdb->update('wp_options',	array('option_value' => 'If A New Comment Is Posted:'), array( 'option_name' => 'subscribe_reloaded_checkbox_label' ), array('%s')); }
			if (get_option('subscribe_reloaded_checked_by_default', '1') == 'yes'){
			global $wpdb;
			$wpdb->update('wp_options', array('option_value' => '2'), array( 'option_name' => 'subscribe_reloaded_checked_by_default' ), array('%s'));
	        global $wpdb;
			$wpdb->update('wp_options',	array('option_value' => 'If A New Comment Is Posted:'), array( 'option_name' => 'subscribe_reloaded_checkbox_label' ), array('%s')); }
			$rtff = get_option('subscribe_reloaded_checkbox_html', ' ');
			if ($rtff != '<p><label for=\'subscribe-reloaded\'> [checkbox_label][checkbox_field]</label></p>' ||  $rtff != '<p><label for=\'subscribe-reloaded\'> [checkbox_field][checkbox_label]</label></p>' ){
			global $wpdb;
			$wpdb->update('wp_options', array('option_value' => '<p><label for=\'subscribe-reloaded\'> [checkbox_label][checkbox_field]</label></p>'), array( 'option_name' => 'subscribe_reloaded_checkbox_html' ), array('%s')); }}
			
register_activation_hook( __FILE__, 'run_at_activation' );
add_action( 'admin_init', 'run_at_activation');

/**
 * Displays the checkbox to allow visitors to subscribe
 */
function subscribe_reloaded_show(){
	global $post, $wp_subscribe_reloaded;
    $is_disabled = get_post_meta($post->ID, 'stcr_disable_subscriptions', true);
	if (!empty($is_disabled))
	return $_comment_ID;
    $show_subscription_box = true;
	$html_to_show = '';
	$user_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '');

	if (function_exists('qtrans_convertURL'))
		$user_link = qtrans_convertURL($user_link);

	$manager_link = (strpos($user_link, '?') !== false)?"$user_link&amp;srp=$post->ID":"$user_link?srp=$post->ID";

	// Load localization files
	load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/send-email-only-on-reply-to-my-comment/langs', '/send-email-only-on-reply-to-my-comment/langs');

	if($wp_subscribe_reloaded->is_user_subscribed($post->ID, '', 'C')){
		$html_to_show = str_replace('[manager_link]', $user_link,
			html_entity_decode(stripslashes(get_option('subscribe_reloaded_subscribed_waiting_label', __("Your subscription to this article needs to be confirmed.",'subscribe-reloaded'))), ENT_COMPAT, 'UTF-8'));
		$show_subscription_box = false;
	}
	elseif($wp_subscribe_reloaded->is_user_subscribed($post->ID, '')){
		$html_to_show = str_replace('[manager_link]', $user_link,
			html_entity_decode(stripslashes(get_option('subscribe_reloaded_subscribed_label', __("You are subscribed to this post. <a href='[manager_link]'>Manage</a> your subscriptions.",'subscribe-reloaded'))), ENT_COMPAT, 'UTF-8'));
		$show_subscription_box = false;
	}
        if ($wp_subscribe_reloaded->is_author($post->post_author)){	// when the second parameter is empty, cookie value will be used
		if (get_option('subscribe_reloaded_admin_subscribe', 'no') == 'no') $show_subscription_box = false;
		$html_to_show .= str_replace('[manager_link]', $manager_link,
			html_entity_decode(stripslashes(get_option('subscribe_reloaded_author_label', __("You can <a href='[manager_link]'>manage the subscriptions</a> of this post.",'subscribe-reloaded'))), ENT_COMPAT, 'UTF-8'));
	}
        if ($show_subscription_box){
		$checkbox_label = str_replace('[subscribe_link]', "$manager_link&amp;sra=s",
		html_entity_decode(stripslashes(get_option('subscribe_reloaded_checkbox_label', __("If A New Comment Is Posted:",'subscribe-reloaded'))), ENT_COMPAT, 'UTF-8'));
		$checkbox_inline_style = get_option('subscribe_reloaded_checkbox_inline_style', 'width:30px');
		if (!empty($checkbox_inline_style)) $checkbox_inline_style = " style='$checkbox_inline_style'";
		$checkbox_html_wrap = html_entity_decode(stripslashes(get_option('subscribe_reloaded_checkbox_html', '')), ENT_COMPAT, 'UTF-8');
		$checkbox_field = "<select name='subscribe-reloaded' id='subscribe-reloaded'><option value='none'".((get_option('subscribe_reloaded_checked_by_default', '1') == '1')?" selected='selected'":'').">".get_option('subscribe_reloaded_dropdown_label', 'Do Not Send Email Notifications.')."</option><option value='yes'".((get_option('subscribe_reloaded_checked_by_default', '1') == '2')?" selected='selected'":'').">".get_option('subscribe_reloaded_dropdown_label1', 'Send Email Notification ONLY If Someone Replies To My Comment(s).')."</option><option value='replies'".((get_option('subscribe_reloaded_checked_by_default', '1') == '3')?" selected='selected'":'').">".get_option('subscribe_reloaded_dropdown_label2', 'Send Email Notification Whenever A New Comment Is Posted.')."</option></select>";
		
		if (get_option('subscribe_reloaded_show_subscription_box1', 'yes') == 'no'){
			$checkbox_label = "";
			} else {
			$checkbox_label = $checkbox_label . "<br />";
			}
	    if (empty($checkbox_html_wrap)){
			$html_to_show = "$checkbox_field <label for='subscribe-reloaded'>$checkbox_label</label>" . $html_to_show;
		}
		else{
			$checkbox_html_wrap = str_replace('[checkbox_field]', $checkbox_field, $checkbox_html_wrap);
			$html_to_show = str_replace('[checkbox_label]', $checkbox_label, $checkbox_html_wrap) . $html_to_show;
		}
	}
	if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) $html_to_show = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($html_to_show);
	echo "<!-- BEGIN: Email notification plugin by Elance360 -->$html_to_show<!-- END: Email notification plugin by Elance360 -->";
}

// Show the checkbox - You can manually override this by adding the corresponding function in your template
if (get_option('subscribe_reloaded_show_subscription_box', 'yes') == 'yes')
{
	if (get_option('subscribe_reloaded_sub_options', 'op1') == 'op1')
	{
	add_action('comment_form', 'subscribe_reloaded_show');
	}
	if (get_option('subscribe_reloaded_sub_options', 'op1') == 'op2')
	{
	add_action('comment_form_after_fields', 'subscribe_reloaded_show');
    add_action('comment_form_logged_in_after', 'subscribe_reloaded_show' );
	}
}
class wp_subscribe_reloaded{

	/**
	 * Constructor -- Sets things up.
	 */
	public function __construct(){
		$this->salt = defined('NONCE_KEY')?NONCE_KEY:'please create a unique key in your wp-config.php';

		// What to do when a new comment is posted
		add_action('comment_post', array(&$this, 'new_comment_posted'), 12, 2);

		// Provide content for the management page using WP filters
		if (!is_admin()){
			$manager_page_permalink = get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
			if (function_exists('qtrans_convertURL')) $manager_page_permalink = qtrans_convertURL($manager_page_permalink);
			if (empty($manager_page_permalink)) $manager_page_permalink = get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
			if ((strpos($_SERVER["REQUEST_URI"], $manager_page_permalink) !== false) && get_option('subscribe_reloaded_manager_page_enabled', 'yes') == 'yes'){
				add_filter('the_posts', array(&$this, 'subscribe_reloaded_manage'), 10, 2);
			}

			// Create a hook to use with the daily cron job
			add_action('subscribe_reloaded_purge', array(&$this,'subscribe_reloaded_purge'));
		}
		else{
			// Initialization routines that should be executed on activation/deactivation
			register_activation_hook(__FILE__, array(&$this, 'activate'));
			register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));

			// Hook for WPMU - New blog created
			add_action('wpmu_new_blog', array(&$this, 'new_blog'), 10, 1);

			// Remove subscriptions attached to a post that is being deleted
			add_action('delete_post', array(&$this, 'delete_subscriptions'), 10, 2);

			// Monitor actions on existing comments
			add_action('deleted_comment', array(&$this, 'comment_deleted'));
			add_action('wp_set_comment_status', array(&$this, 'comment_status_changed'));
			

			// Subscribe post authors, if the case
			if (get_option('subscribe_reloaded_notify_authors', 'no') == 'yes'){
				add_action('publish_post', array(&$this, 'subscribe_post_author'));
			}

			// Add a new column to the Edit Comments panel
			add_filter('manage_edit-comments_columns', array(&$this,'add_column_header'));
			add_filter('manage_posts_columns', array(&$this,'add_column_header'));
			add_action('manage_comments_custom_column', array(&$this,'add_comment_column'));
			add_action('manage_posts_custom_column', array(&$this,'add_post_column'));

			// Add appropriate entries in the admin menu
			add_action('admin_menu', array(&$this, 'add_config_menu'));
			add_action('admin_print_styles-send-email-only-on-reply-to-my-comment/options/index.php', array(&$this, 'add_options_stylesheet'));
			add_action('admin_print_styles-edit-comments.php', array(&$this, 'add_post_comments_stylesheet'));
			add_action('admin_print_styles-edit.php', array(&$this, 'add_post_comments_stylesheet'));

			// Contextual help
			add_action('contextual_help', array(&$this,'contextual_help'), 10, 3);

			// Shortcodes to use the management URL sitewide
			add_shortcode('subscribe-url', array(&$this,'subscribe_url_shortcode'));
		}
	}
	// end __construct

	/**
	 * Support for WP MU network activations (experimental)
	 */
	public function activate(){
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite() && isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)){
			$blogids = $wpdb->get_col($wpdb->prepare("
				SELECT blog_id
				FROM $wpdb->blogs
				WHERE site_id = %d
				AND deleted = 0
				AND spam = 0", $wpdb->siteid));

			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				$this->_activate();
			}
			restore_current_blog();
		}
		else{
			$this->_activate();
		}
	}
	// end activate
	 
	/**
	 * Support for WP MU network activations (experimental)
	 */
	public function new_blog($_blog_id){
		switch_to_blog($_blog_id);
		$this->_activate();
		restore_current_blog();
	}
	// end new_blog

	/**
	 * Adds the options to the database and imports the data from other plugins
	 */
	private function _activate(){
		global $wpdb;

		// Load localization files
		load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/send-email-only-on-reply-to-my-comment/langs', '/send-email-only-on-reply-to-my-comment/langs');

		// Starting from version 2.0 StCR uses Wordpress' tables to store the information about subscriptions
		$this->_update_db();

		// Messages related to the management page
		global $wp_rewrite;
        if (empty($wp_rewrite->permalink_structure)){
			add_option('subscribe_reloaded_manager_page', '/?page_id=99999', '', 'no');
		}
		else{
			add_option('subscribe_reloaded_manager_page', '/comment-subscriptions', '', 'no');
		}
        add_option('subscribe_reloaded_dropdown_label', __("Do Not Send Email Notifications.",'subscribe-reloaded'), '', 'no');
        add_option('subscribe_reloaded_dropdown_label1', __("Send Email Notification ONLY If Someone Replies To My Comment(s).",'subscribe-reloaded'), '', 'no');
        add_option('subscribe_reloaded_dropdown_label2', __("Send Email Notification Whenever A New Comment Is Posted.",'subscribe-reloaded'), '', 'no');
        add_option('subscribe_reloaded_show_subscription_box1', 'yes', '', 'no');
		add_option('subscribe_reloaded_show_subscription_box', 'yes', '', 'no');
        add_option('subscribe_reloaded_sub_options', 'op1', '', 'no');
		add_option('subscribe_reloaded_show_subscription_box', 'yes', '', 'no');
		add_option('subscribe_reloaded_checked_by_default', '1', '', 'no');
		add_option('subscribe_reloaded_enable_advanced_subscriptions', 'yes', '', 'yes');
		add_option('subscribe_reloaded_checkbox_inline_style', 'width:30px', '', 'no');
		add_option('subscribe_reloaded_checkbox_html', "<p><label for='subscribe-reloaded'> [checkbox_label][checkbox_field]</label></p>", '', 'no');
		add_option('subscribe_reloaded_checkbox_label', __("If A New Comment Is Posted:",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_subscribed_label', __("You are subscribed to this post. <a href='[manager_link]'>Manage</a> your subscriptions.",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_subscribed_waiting_label', __("Your subscription to this article needs to be confirmed.",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_author_label', __("You can <a href='[manager_link]'>manage the subscriptions</a> of this post.",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_manager_page_enabled', 'yes', '', 'no');
		add_option('subscribe_reloaded_manager_page_title', __('Manage subscriptions','subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_custom_header_meta', "<meta name='robots' content='noindex,nofollow'>", '', 'no');
		add_option('subscribe_reloaded_request_mgmt_link', __('To Manage your subscriptions, please enter your email address here below. We will send you a message containing the link to access your personal management page.', 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_request_mgmt_link_thankyou', __('Thank you for using our subscription service. Your request has been completed, and you should receive an email with the management link in a few minutes.', 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_subscribe_without_commenting', __(" ", 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_subscription_confirmed', __("Thank you for using our subscription service. Your request has been completed. You will receive a notification email every time a new comment to this article is approved and posted by the administrator.", 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_subscription_confirmed_dci', __("Thank you for using our subscription service. In order to confirm your request, please check your email for the verification message and follow the instructions.", 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_author_text', __("To change one or more notifications, select the corresponding checkbox and then click the action that you want to perform.", 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_user_text', __("To change one or more notifications, select the corresponding checkbox and then click the action that you want to perform. You are currently subscribed to:", 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_from_name', get_bloginfo('name'), '', 'no');
		add_option('subscribe_reloaded_from_email', get_bloginfo('admin_email'), '', 'no');
		add_option('subscribe_reloaded_notification_subject', __('There is a new comment to [post_title]','subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_notification_content', __("There is a new comment to [post_title].\nComment Link: [comment_permalink]\nAuthor: [comment_author]\nComment:\n[comment_content]\nPermalink: [post_permalink]\nManage your subscriptions: [manager_link]",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_double_check_subject', __('Please confirm your subscription to [post_title]','subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_double_check_content', __("",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_management_subject', __('Manage your subscriptions on [blog_name]','subscribe-reloaded'));
		add_option('subscribe_reloaded_management_content', __("You have requested to manage your subscriptions to the articles on [blog_name]. Follow this link to access your personal page:\n[manager_link]",'subscribe-reloaded'));
		add_option('subscribe_reloaded_purge_days', '30', '', 'no');
		add_option('subscribe_reloaded_enable_double_check', 'no', '', 'no');
		add_option('subscribe_reloaded_notify_authors', 'no', '', 'no');
		add_option('subscribe_reloaded_enable_html_emails', 'no', '', 'no');
		add_option('subscribe_reloaded_process_trackbacks', 'no', '', 'no');
		add_option('subscribe_reloaded_enable_admin_messages', 'no', '', 'no');
		add_option('subscribe_reloaded_admin_subscribe', 'no', '', 'no');

		// Schedule the autopurge hook
		if (!wp_next_scheduled('subscribe_reloaded_purge'))
			wp_schedule_event(time(), 'daily', 'subscribe_reloaded_purge');
	}
	// end _activate

	/**
	 * Performs some clean-up maintenance (disable cron job).
	 */
	public function deactivate() {
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite() && isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)){
			$blogids = $wpdb->get_col($wpdb->prepare("
				SELECT blog_id
				FROM $wpdb->blogs
				WHERE site_id = %d
				AND deleted = 0
				AND spam = 0", $wpdb->siteid));

			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				wp_clear_scheduled_hook('subscribe_reloaded_purge');
			}
			restore_current_blog();
		}
		else{
			wp_clear_scheduled_hook('subscribe_reloaded_purge');
		}
	}
	// end deactivate

	/**
	 * Takes the appropriate action, when a new comment is posted
	 */
	public function new_comment_posted($_comment_ID = 0, $_comment_status = 0){
		// Retrieve the information about the new comment
		$info = $this->_get_comment_object($_comment_ID);

		if (empty($info) || $info->comment_approved == 'spam')
			return $_comment_ID;
		
		// Are subscriptions allowed for this post?
		$is_disabled = get_post_meta($info->comment_post_ID, 'stcr_disable_subscriptions', true);
		if (!empty($is_disabled))
			return $_comment_ID;

		// Process trackbacks and pingbacks?
		if ((get_option('subscribe_reloaded_process_trackbacks', 'no') == 'no') && ($info->comment_type == 'trackback' || $info->comment_type == 'pingback'))
			return $_comment_ID;

		// Did this visitor request to be subscribed to the discussion? (and s/he is not subscribed)
		if (!empty($_POST['subscribe-reloaded']) && !empty($info->comment_author_email)){
			if (!in_array($_POST['subscribe-reloaded'], array('none', 'yes', 'replies')))
				return $_comment_ID;

			switch ($_POST['subscribe-reloaded']){
				case 'none':
					$status = 'N';
					break;
				case 'yes':
					$status = 'Y';
					break;
				case 'replies':
					$status = 'R';
					break;
			}

			if (!$this->is_user_subscribed($info->comment_post_ID, $info->comment_author_email)){
				if($this->isDoubleCheckinEnabled($info)) {
					$this->sendConfirmationEMail($info);
					$status = "{$status}C";
				}
				$this->add_subscription($info->comment_post_ID, $info->comment_author_email, $status);
				
				// If comment is in the moderation queue
				if ($info->comment_approved == 0){
					//don't send notification-emails to all subscribed users
					return $_comment_ID;
				}
			}
		}
		global $wpdb;
		$check_if_send_email =	$wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` = '1' AND `meta_key` = '_stt@_notsend_dup'");
		if ($check_if_send_email == null){
		global $wpdb;
		$get_last_comment1 = $wpdb->get_var("SELECT `comment_ID` FROM `wp_comments` WHERE `comment_approved` = '1' ORDER BY `comment_ID` DESC");
		if ($get_last_comment1 != null){
		$a_p = "1";
		$a_K = "_stt@_notsend_dup";
		global $wpdb;
		$wpdb->insert('wp_postmeta', array('post_id' => $a_p, 'meta_key' => $a_K, 'meta_value' => $get_last_comment1), array( '%s', '%s', '%s')); }	}
		global $wpdb;
		$check_if_send_email =	$wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` = '1' AND `meta_key` = '_stt@_notsend_dup'");
		if ($check_if_send_email != NULL && $_comment_ID > $check_if_send_email){
		$cpid = $info->comment_post_ID;
		$cpemail = $info->comment_author_email;
		$cpcid = $_comment_ID;
		global $wpdb;
		$emailall = $wpdb->get_col("SELECT `meta_key` FROM `wp_postmeta` WHERE `post_id` = '$cpid' AND `meta_value` LIKE '%%R'");
        if ($emailall){
			        foreach($emailall as $childemail2244){
					$childemail22 = str_replace("_stcr@_", "", "$childemail2244");
					if ($childemail22 != $info->comment_author_email){
					global $wpdb;
					$checkalready = $wpdb->get_col("SELECT `post_id` FROM `wp_postmeta` WHERE `post_id` = '$cpid' AND `meta_key` = '_stt@_$childemail22' AND `meta_value` = '$cpcid'");
					if (!($checkalready)){
					$this->notify_user($info->comment_post_ID, $childemail22, $_comment_ID);
					$childemail22345 = "_stt@_$childemail22";
					global $wpdb;
					$wpdb->insert('wp_postmeta', array('post_id' => $cpid, 'meta_key' => $childemail22345, 'meta_value' => $cpcid), array( '%s', '%s', '%s') );	}}}}
					$y_cpid = $info->comment_post_ID;
		            $y_cpemail = $info->comment_author_email;
		            $y_cpcid = $_comment_ID;
		        global $wpdb;
    			$y_email = $wpdb->get_var("SELECT `comment_parent` FROM `wp_comments` WHERE `comment_ID` = '$y_cpcid'");
				if ($y_email != '0'){
				global $wpdb;
				$y_parent = $wpdb->get_row("SELECT * FROM `wp_comments` WHERE `comment_ID` = '$y_email'", ARRAY_N);
				if ($y_parent != NULL){
			    global $wpdb;
			    $y_parent_check = $wpdb->get_row("SELECT * FROM `wp_postmeta` WHERE `post_id` = '$y_cpid' AND `meta_key` = '_stcr@_$y_parent[3]' AND `meta_value` LIKE '%%Y'", ARRAY_N);
				if ($y_parent_check != NULL){
				global $wpdb;
				$checkalready_y = $wpdb->get_col("SELECT `post_id` FROM `wp_postmeta` WHERE `post_id` = '$y_cpid' AND `meta_key` = '_stt@_$y_parent[3]' AND `meta_value` = '$y_cpcid'");
				if (!($checkalready_y)){
				$this->notify_user($info->comment_post_ID, $y_parent[3], $y_cpcid);
				$y_email2245 = "_stt@_$y_parent[3]";
	            global $wpdb;				 
                $wpdb->insert('wp_postmeta', array( 'post_id' => $y_cpid, 'meta_key' => $y_email2245, 'meta_value' => $y_cpcid), array( '%s', '%s', '%s') );}}}}}}
				
	// end new_comment_posted
		
	public function isDoubleCheckinEnabled($info) {
		$approved_subscriptions = $this->get_subscriptions(array('status', 'email'), array('equals', 'equals'), array('Y', $info->comment_author_email));
		if ((get_option('subscribe_reloaded_enable_double_check', 'no') == 'yes') && !is_user_logged_in() && empty($approved_subscriptions)){
			return true;
		}
		else {
			return false;
		}
	}
	
	public function sendConfirmationEMail($info) {
		// Retrieve the information about the new comment
		$this->confirmation_email($info->comment_post_ID, $info->comment_author_email);
	}
	
	/**
	 * Performs the appropriate action when the status of a given comment changes
	 */
	public function comment_status_changed($_comment_ID = 0, $_comment_status = 0){
		
		// Retrieve the information about the comment
		$info = $this->_get_comment_object($_comment_ID);
		if (empty($info))
			return $_comment_ID;

		switch($info->comment_approved){
			case '0': // Unapproved: change the status of the corresponding subscription (if exists) to 'pending'
				$this->update_subscription_status($info->comment_post_ID, $info->comment_author_email, 'C');
				break;

			case '1': // Approved
				$this->update_subscription_status($info->comment_post_ID, $info->comment_author_email, '-C');
				global $wpdb;
				$check_if_send_email =	$wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` = '1' AND `meta_key` = '_stt@_notsend_dup'");
				if ($check_if_send_email == null){
		global $wpdb;
		$get_last_comment1 = $wpdb->get_var("SELECT `comment_ID` FROM `wp_comments` WHERE `comment_approved` = '1' ORDER BY `comment_ID` DESC");
		if ($get_last_comment1 != null){
		$a_p = "1";
		$a_K = "_stt@_notsend_dup";
		global $wpdb;
		$wpdb->insert('wp_postmeta', array( 'post_id' => $a_p, 'meta_key' => $a_K, 'meta_value' => $get_last_comment1), array( '%s', '%s', '%s') );}}
				global $wpdb;
				$check_if_send_email =	$wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` = '1' AND `meta_key` = '_stt@_notsend_dup'");
				if ($check_if_send_email != NULL && $_comment_ID > $check_if_send_email){
		        $cpid = $info->comment_post_ID;
		        $cpemail = $info->comment_author_email;
		        $cpcid = $_comment_ID;
				global $wpdb;
			    $emailall = $wpdb->get_col("SELECT `meta_key` FROM `wp_postmeta` WHERE `post_id` = '$cpid' AND `meta_value` LIKE '%%R'");
                if ($emailall){
					foreach($emailall as $childemail2244){
					$childemail22 = str_replace("_stcr@_", "", "$childemail2244");
					if ($childemail22 != $info->comment_author_email){
					global $wpdb;
					$checkalready = $wpdb->get_col("SELECT `post_id` FROM `wp_postmeta` WHERE `post_id` = '$cpid' AND `meta_key` = '_stt@_$childemail22' AND `meta_value` = '$cpcid'");
					if (!($checkalready)){
					$this->notify_user($info->comment_post_ID, $childemail22, $_comment_ID);
					$childemail22345 = "_stt@_$childemail22";
					global $wpdb;
					$wpdb->insert('wp_postmeta', array( 'post_id' => $cpid, 'meta_key' => $childemail22345, 'meta_value' => $cpcid), array( '%s', '%s', '%s') );}}
			if ($childemail22 == $info->comment_author_email){
			global $wpdb;	
			$commentsbefore = $wpdb->get_col("SELECT `comment_ID` FROM `wp_comments` WHERE `comment_post_ID` = '$cpid' AND `comment_ID` > '$cpcid' AND `comment_approved` = '1'");
			global $wpdb;
			$comment_dup = $wpdb->get_col("SELECT `comment_author_email` FROM `wp_comments` WHERE `comment_post_ID` = '$cpid' AND `comment_ID` > '$cpcid' AND `comment_author_email` = '$childemail22'");
			if ($commentsbefore && !($comment_dup)){
				    foreach($commentsbefore as $greatercomment){
					global $wpdb;
					$checkalready_1 = $wpdb->get_col("SELECT `post_id` FROM `wp_postmeta` WHERE `post_id` = '$cpid' AND `meta_key` = '_stt@_$childemail22' AND `meta_value` = '$greatercomment'");
					if (!($checkalready_1)){
					$this->notify_user($info->comment_post_ID, $childemail22, $greatercomment);
					$childemail22345 = "_stt@_$childemail22";
	                global $wpdb;				 
                    $wpdb->insert('wp_postmeta', array( 'post_id' => $cpid, 'meta_key' => $childemail22345, 'meta_value' => $greatercomment), array( '%s', '%s', '%s') );}}}}}}
				    $y_cpid = $info->comment_post_ID;
		            $y_cpemail = $info->comment_author_email;
		            $y_cpcid = $_comment_ID;
		     	global $wpdb;
		    	$y_email = $wpdb->get_var("SELECT `comment_parent` FROM `wp_comments` WHERE `comment_ID` = '$y_cpcid'");
				if ($y_email != '0'){
				global $wpdb;
				$y_parent = $wpdb->get_row("SELECT * FROM `wp_comments` WHERE `comment_ID` = '$y_email'", ARRAY_N);
				if ($y_parent != NULL){
				global $wpdb;
			    $y_parent_check = $wpdb->get_row("SELECT * FROM `wp_postmeta` WHERE `post_id` = '$y_cpid' AND `meta_key` = '_stcr@_$y_parent[3]' AND `meta_value` LIKE '%%Y'", ARRAY_N);
				if ($y_parent_check != NULL){
				global $wpdb;
				$checkalready_y = $wpdb->get_col("SELECT `post_id` FROM `wp_postmeta` WHERE `post_id` = '$y_cpid' AND `meta_key` = '_stt@_$y_parent[3]' AND `meta_value` = '$y_cpcid'");
				if (!($checkalready_y)){
                $this->notify_user($info->comment_post_ID, $y_parent[3], $y_cpcid);
	            $y_email2245 = "_stt@_$y_parent[3]";
	            global $wpdb;				 
                $wpdb->insert('wp_postmeta', array( 'post_id' => $y_cpid, 'meta_key' => $y_email2245, 'meta_value' => $y_cpcid), array( '%s', '%s', '%s') );}}}}}
				break;
			case 'trash':
			case 'spam':
				$this->comment_deleted($_comment_ID);
				break;
			default:
				break;
		}
		return $_comment_ID;
	}
	// end comment_status

	/**
	 * Performs the appropriate action when a comment is deleted
	 */
	public function comment_deleted($_comment_ID){
		global $wpdb;

		$info = $this->_get_comment_object($_comment_ID);
		if (empty($info))
			return $_comment_ID;

		// Are there any other approved comments sent by this user?
		$count_approved_comments = $wpdb->get_var("
			SELECT COUNT(*)
			FROM $wpdb->comments
			WHERE comment_post_ID = '$info->comment_post_ID' AND comment_author_email = '$info->comment_author_email' AND comment_approved = 1");
		if (intval($count_approved_comments) == 0)
			$this->delete_subscriptions($info->comment_post_ID, $info->comment_author_email);

		return $_comment_ID;
	}
	// end comment_deleted

	/**
	 * Subscribes the post author, if the corresponding option is set
	 */
	public function subscribe_post_author($_post_ID){
		$new_post = get_post($_post_ID);
		$author_email = get_the_author_meta('user_email', $new_post->post_author);
		if (!empty($author_email)){
			$this->add_subscription($_post_ID, $author_email, 'Y');
		}
	}
	// end subscribe_post_author

	/**
	 * Displays the appropriate management page
	 */
	public function subscribe_reloaded_manage($_posts = '', $_query = ''){
		global $current_user;

		if (!empty($_posts))
			return $_posts;

		$post_ID = !empty($_POST['srp'])?intval($_POST['srp']):(!empty($_GET['srp'])?intval($_GET['srp']):0);

		// Is the post_id passed in the query string valid?
		$target_post = get_post($post_ID);
		if (($post_ID > 0) && !is_object($target_post))
			return $_posts;

		// Load localization files
		load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/send-email-only-on-reply-to-my-comment/langs', '/send-email-only-on-reply-to-my-comment/langs');

		$action = !empty($_POST['sra'])?$_POST['sra']:(!empty($_GET['sra'])?$_GET['sra']:0);
		$key = !empty($_POST['srk'])?$_POST['srk']:(!empty($_GET['srk'])?$_GET['srk']:0);

		$email = $this->clean_email(!empty($_POST['sre'])?urldecode($_POST['sre']):(!empty($_GET['sre'])?$_GET['sre']:''));
		if (empty($email) && !empty($current_user->user_email))
			$email = $this->clean_email($current_user->user_email);

		// Subscribe without commenting
		if (!empty($action) && ($action == 's') && ($post_ID > 0)){
			$include_post_content = include(WP_PLUGIN_DIR.'/send-email-only-on-reply-to-my-comment/templates/subscribe.php');
		}

		// Management page for post authors
		elseif (($post_ID > 0) && $this->is_author($target_post->post_author)){
			$include_post_content = include(WP_PLUGIN_DIR.'/send-email-only-on-reply-to-my-comment/templates/author.php');
		}

		// Confirm your subscription (double check-in)
		elseif ( ($post_ID > 0) && !empty($email) && !empty($key) && !empty($action) &&
				$this->is_user_subscribed($post_ID, $email, 'C') &&
				$this->_is_valid_key($key, $email) &&
				($action == 'c') ){
			$include_post_content = include(WP_PLUGIN_DIR.'/send-email-only-on-reply-to-my-comment/templates/confirm.php');
		}

		// Manage your subscriptions (user)
		elseif ( !empty($email) && ((!empty($key) && $this->_is_valid_key($key, $email)) || current_user_can('read')) ){
			$include_post_content = include(WP_PLUGIN_DIR.'/send-email-only-on-reply-to-my-comment/templates/user.php');
		}

		if (empty($include_post_content))
			$include_post_content = include(WP_PLUGIN_DIR.'/send-email-only-on-reply-to-my-comment/templates/request-management-link.php');

		global $wp_query;

		$manager_page_title = html_entity_decode(get_option('subscribe_reloaded_manager_page_title', 'Manage subscriptions'), ENT_COMPAT, 'UTF-8');
		if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage'))
			$manager_page_title = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($manager_page_title);

		$posts[] =
			(object)array(
				'ID' => '9999999',
				'post_author' => '1',
				'post_date' => '2001-01-01 11:38:56',
				'post_date_gmt' => '2001-01-01 00:38:56',
				'post_content' => $include_post_content,
				'post_title' => $manager_page_title,
				'post_excerpt' => '',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_password' => '',
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => '2001-01-01 11:00:01',
				'post_modified_gmt' => '2001-01-01 00:00:01',
				'post_content_filtered' => '',
				'post_parent' => '0',
				'menu_order' => '0',
				'post_type' => 'page',
				'post_mime_type' => '',
				'post_category' => '0',
				'comment_count' => '0',
				'filter' => 'raw',
				'guid' => get_bloginfo('url').'/?page_id=9999999',
				'post_name' => get_bloginfo('url').'/?page_id=9999999',
				'ancestors' => array()
			);

		// Make WP believe this is a real page, with no comments attached
		$wp_query->is_page = true;
		$wp_query->is_single = false;
		$wp_query->is_home = false;
		$wp_query->comments = false;

		// Discard 404 errors thrown by other checks
		unset($wp_query->query["error"]);
		$wp_query->query_vars["error"]="";
		$wp_query->is_404=false;

		// Seems like WP adds its own HTML formatting code to the content, we don't need that here
		remove_filter('the_content','wpautop');
		add_action('wp_head', array(&$this, 'add_custom_header_meta'));

		return $posts;
	}
	// end subscribe_reloaded_manage

	/**
	 * Removes old entries from the database
	 */
	public function subscribe_reloaded_purge() {
		global $wpdb;

		if (($autopurge_interval = intval(get_option('subscribe_reloaded_purge_days', 0))) <= 0)
			return true;

		// Delete old entries
		$wpdb->query("
			DELETE FROM $wpdb->postmeta
			WHERE meta_key LIKE '\_stcr@\_%'
				AND STR_TO_DATE(meta_value, '%Y-%m-%d %H:%i:%s') <= DATE_SUB(NOW(), INTERVAL $autopurge_interval DAY) AND meta_value LIKE '%C'");
	}
	// end subscribe_reloaded_purge

	/**
	 * Checks if current logged in user is the author of this post
	 */
	public function is_author($_post_author){
		global $current_user;

		return (!empty($current_user) && (($_post_author == $current_user->ID) || current_user_can('manage_options')));
	}
	// end is_author

	/**
	 * Checks if a given email address is subscribed to a post
	 */
	public function is_user_subscribed($_post_ID = 0, $_email = '', $_status = ''){
		global $current_user;

		if ((empty($current_user->user_email) && empty($_COOKIE['comment_author_email_'. COOKIEHASH]) && empty($_email)) || empty($_post_ID))
			return false;

		$operator = ($_status != '')?'equals':'contains';
		$subscriptions = $this->get_subscriptions(array('post_id', 'status'), array('equals', $operator), array($_post_ID, $_status));

		if(empty($_email))
			$user_email = !empty($current_user->user_email)?$current_user->user_email:(!empty($_COOKIE['comment_author_email_'. COOKIEHASH])?stripslashes(esc_attr($_COOKIE['comment_author_email_'. COOKIEHASH])):'#undefined#');
		else
			$user_email = $_email;

		foreach($subscriptions as $a_subscription)
			if ($user_email == $a_subscription->email)
				return true;

		return false;
	}
	// end is_user_subscribed

	/**
	 * Adds a new subscription
	 */
	public function add_subscription($_post_id = 0, $_email = '', $_status = 'Y'){
		global $wpdb, $post_id;

		// Does the post exist?
		$target_post = get_post($post_id);
		if (($post_id > 0) && !is_object($target_post))
			return;

		// Filter unwanted statuses
		if (!in_array($_status, array('Y', 'YC', 'R', 'RC', 'C', '-C')) || empty($_status))
			return;

		// Using Wordpress local time
		$dt = date_i18n('Y-m-d H:i:s');

		$clean_email = $this->clean_email($_email);
		$wpdb->query($wpdb->prepare("
			INSERT IGNORE INTO $wpdb->postmeta (post_id, meta_key, meta_value)
				SELECT %d, %s, %s
				FROM DUAL
				WHERE NOT EXISTS (
					SELECT post_id
					FROM $wpdb->postmeta
					WHERE post_id = %d
						AND meta_key = %s
					LIMIT 0,1
				)", $_post_id, "_stcr@_$clean_email", "$dt|$_status", $_post_id, "_stcr@_$clean_email"));
	}
	// end add_subscription

	/**
	 * Deletes one or more subscriptions from the database
	 */
		public function delete_subscriptions($_post_id = 0, $_email = ''){
	    global $wpdb;

		if (empty($_post_id))
			return 0;

		$posts_where = '';
		if (!is_array($_post_id)){
			$posts_where = "post_id = ".intval($_post_id);
		}
		else{
			foreach ($_post_id as $a_post_id)
				$posts_where .= "post_id = '".intval($a_post_id)."' OR ";

			$posts_where = substr($posts_where, 0, -4);
		}

		if (!empty($_email)){
			$emails_where = '';
			if (!is_array($_email)){
				$emails_where = "meta_key = '_stcr@_".$this->clean_email($_email)."'";
			}
			else{
				foreach ($_email as $a_email)
					$emails_where .= "meta_key = '_stcr@_".$this->clean_email($a_email)."' OR ";

				$emails_where = substr($emails_where, 0, -4);
			}
			return $wpdb->query("DELETE FROM $wpdb->postmeta WHERE ($posts_where) AND ($emails_where)");
		}
		else
			return $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '\_stcr@\_%' AND ($posts_where)");
	}
	// end delete_subscriptions

	/**
	 * Updates the status of an existing subscription
	 */
	public function update_subscription_status($_post_id = 0, $_email = '', $_new_status = 'C'){
		global $wpdb;

		// Filter unwanted statuses
		if (empty($_new_status) || !in_array($_new_status, array('Y', 'R', 'C', '-C')) || empty($_email))
			return 0;

		if (!empty($_post_id)){
			$posts_where = '';
			if (!is_array($_post_id)){
				$posts_where = "post_id = ".intval($_post_id);
			}
			else{
				foreach ($_post_id as $a_post_id)
					$posts_where .= "post_id = '".intval($a_post_id)."' OR ";

				$posts_where = substr($posts_where, 0, -4);
			}
		}
		else{ // Mass update subscriptions
			$posts_where = '1=1';
		}

		$emails_where = '';
		if (!is_array($_email)){
			$emails_where = "meta_key = '_stcr@_".$this->clean_email($_email)."'";
		}
		else{
			foreach ($_email as $a_email)
				$emails_where .= "meta_key = '_stcr@_".$this->clean_email($a_email)."' OR ";

			$emails_where = substr($emails_where, 0, -4);
		}

		$meta_length = (strpos($_new_status,'C')!==false)?21:20;
		$new_status = ($_new_status == '-C')?'':$_new_status;

		return $wpdb->query("
			UPDATE $wpdb->postmeta
			SET meta_value = CONCAT(SUBSTRING(meta_value, 1, $meta_length), '$new_status')
			WHERE ($posts_where) AND ($emails_where)");
	}
	// end update_subscription_status



	/**
	 * Updates the email address of an existing subscription
	 */
	public function update_subscription_email($_post_id = 0, $_email = '', $_new_email = ''){
		global $wpdb;

		// Nothing to do if the new email hasn't been specified
		if (empty($_email) || empty($_new_email) || strpos($_new_email, '@') == 0)
			return;

		$clean_values[] = "_stcr@_".$this->clean_email($_new_email);
		$clean_values[] = "_stcr@_".$this->clean_email($_email);
		$post_where = '';
		if (!empty($_post_id)){
			$post_where = ' AND post_id = %d';
			$clean_values[] = $_post_id;
		}

		return $wpdb->query($wpdb->prepare("
			UPDATE $wpdb->postmeta
			SET meta_key = %s
			WHERE meta_key = %s $post_where", $clean_values));
	}
	// end update_subscription_email

	/**
	 * Retrieves a list of emails subscribed to this post
	 */
	public function get_subscriptions($_search_field = array('email'), $_operator = array('equals'), $_search_value = array(''), $_order_by = 'dt', $_order = 'ASC',  $_offset = 0, $_limit_results = 0){
		global $wpdb;

		// Type adjustments
		$search_fields = (!is_array($_search_field))?array($_search_field):$_search_field;
		$operators = (!is_array($_operator))?array($_operator):$_operator;
		$search_values = (!is_array($_search_value))?array($_search_value):$_search_value;

		// Find if exists a 'replies only' subscription for the parent comment
		if ($search_fields[0] == 'parent'){
			return $wpdb->get_results($wpdb->prepare("
				SELECT pm.meta_id, REPLACE(pm.meta_key, '_stcr@_', '') AS email, pm.post_id, SUBSTRING(pm.meta_value, 1, 19) AS dt, SUBSTRING(pm.meta_value, 21) AS status
				FROM $wpdb->postmeta pm INNER JOIN $wpdb->comments c ON pm.post_id = c.comment_post_ID
				WHERE pm.meta_key LIKE '\_stcr@\_%%'
					AND pm.meta_value LIKE '%%R'
					AND c.comment_ID = %d", $search_values[0]), OBJECT);
		}
		else{
			$where_clause = '';
			foreach($search_fields as $a_idx => $a_field){
				$where_clause .= ' AND';
				$offset_status = ($a_field == 'status' && $search_values[$a_idx] == 'C')?22:21;
				switch ($a_field){
					case 'status':
						$where_clause .= " SUBSTRING(meta_value, $offset_status)";
						break;
					case 'post_id':
						$where_clause .= ' post_id';
						break;
					default:
						$where_clause .= ' SUBSTRING(meta_key, 8)';
				}
				switch ($operators[$a_idx]){
					case 'equals':
						$where_clause .= " = %s";
						$where_values[] = $search_values[$a_idx];
						break;
					case 'does not contain':
						$where_clause .= " NOT LIKE %s";
						$where_values[] = "%{$search_values[$a_idx]}%";
						break;
					case 'starts with':
						$where_clause .= " LIKE %s";
						$where_values[] = "${$search_values[$a_idx]}%";
						break;
					case 'ends with':
						$where_clause .= " LIKE %s";
						$where_values[] = "%{$search_values[$a_idx]}";
						break;
					default: // contains
						$where_clause .= " LIKE %s";
						$where_values[] = "%{$search_values[$a_idx]}%";
				}
			}
			switch ($_order_by){
				case 'status':
					$order_by = "status";
					break;
				case 'email':
					$order_by = 'meta_key';
					break;
				case 'dt':
					$order_by = 'dt';
					break;
				default:
					$order_by = 'post_id';
			}
			$order = ($_order != 'ASC' && $_order != 'DESC')?'DESC':$_order;

			// This is the 'official' way to have an offset without a limit
			$row_count = ($_limit_results <= 0)?'18446744073709551610':$_limit_results;

			return $wpdb->get_results($wpdb->prepare("
				SELECT meta_id, REPLACE(meta_key, '_stcr@_', '') AS email, post_id, SUBSTRING(meta_value, 1, 19) AS dt, SUBSTRING(meta_value, 21) AS status
				FROM $wpdb->postmeta
				WHERE meta_key LIKE '\_stcr@\_%%' $where_clause
				ORDER BY $order_by $order
				LIMIT $_offset,$row_count", $where_values), OBJECT);
		}
	}
	// end get_subscriptions

	/**
	 * Sends a message to confirm a subscription
	 */
	public function confirmation_email($_post_ID = 0, $_email = ''){
		// Retrieve the options from the database
		$from_name = stripslashes(get_option('subscribe_reloaded_from_name', 'admin'));
		$from_email = get_option('subscribe_reloaded_from_email', get_bloginfo('admin_email'));
		$subject = html_entity_decode(stripslashes(get_option('subscribe_reloaded_double_check_subject', 'Please confirm your subscribtion to [post_title]')), ENT_COMPAT, 'UTF-8');
		$message = html_entity_decode(stripslashes(get_option('subscribe_reloaded_double_check_content', '')), ENT_COMPAT, 'UTF-8');
		$manager_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
		if (function_exists('qtrans_convertURL')) $manager_link = qtrans_convertURL($manager_link);

		$clean_email = $this->clean_email($_email);
		$subscriber_salt = $this->generate_key($clean_email);

		$manager_link .= ((strpos($manager_link, '?') !== false)?'&':'?')."sre=".urlencode($clean_email)."&srk=$subscriber_salt";
		$confirm_link = "$manager_link&srp=$_post_ID&sra=c";

		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: $from_name <$from_email>\n";
		$content_type = (get_option('subscribe_reloaded_enable_html_emails', 'no') == 'yes')?'text/html':'text/plain';
		$headers .= "Content-Type: $content_type; charset=".get_bloginfo('charset')."\n";

		$post = get_post($_post_ID);
		$post_permalink = get_permalink($_post_ID);

		// Replace tags with their actual values
		$subject = str_replace('[post_title]', $post->post_title, $subject);

		$message = str_replace('[post_permalink]', $post_permalink, $message);
		$message = str_replace('[confirm_link]', $confirm_link, $message);
		$message = str_replace('[manager_link]', $manager_link, $message);

		// QTranslate support
		if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){
			$subject = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($subject);
			$message = str_replace('[post_title]', qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($post->post_title), $message);
			$message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
		}
		else{
			$message = str_replace('[post_title]', $post->post_title, $message);
		}
		if($content_type == 'text/html') $message = $this->wrap_html_message($message, $subject);

		wp_mail($clean_email, $subject, $message, $headers);
	}
	// end confirmation_email

	/**
	 * Sends the notification message to a given user
	 */
	public function notify_user($_post_ID = 0, $_email = '', $_comment_ID = 0){
		// Retrieve the options from the database
		$from_name = html_entity_decode(stripslashes(get_option('subscribe_reloaded_from_name', 'admin')), ENT_COMPAT, 'UTF-8');
		$from_email = get_option('subscribe_reloaded_from_email', get_bloginfo('admin_email'));
		$subject = html_entity_decode(stripslashes(get_option('subscribe_reloaded_notification_subject', 'There is a new comment on the post [post_title]')), ENT_COMPAT, 'UTF-8');
		$message = html_entity_decode(stripslashes(get_option('subscribe_reloaded_notification_content', '')), ENT_COMPAT, 'UTF-8');
		$manager_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
		if (function_exists('qtrans_convertURL'))
			$manager_link = qtrans_convertURL($manager_link);

		$clean_email = $this->clean_email($_email);
		$subscriber_salt = $this->generate_key($clean_email);
		
		$manager_link .= ((strpos($manager_link, '?') !== false)?'&':'?')."sre=".urlencode($clean_email)."&srk=$subscriber_salt";

		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: $from_name <$from_email>\n";
		$content_type = (get_option('subscribe_reloaded_enable_html_emails', 'no') == 'yes')?'text/html':'text/plain';
		$headers .= "Content-Type: $content_type; charset=".get_bloginfo('charset')."\n";

		$post = get_post($_post_ID);
		$comment = get_comment($_comment_ID);
		$post_permalink = get_permalink( $_post_ID );
		$comment_permalink = get_comment_link($_comment_ID);

		// Replace tags with their actual values
		$subject = str_replace('[post_title]', $post->post_title, $subject);

		$message = str_replace('[post_permalink]', $post_permalink, $message);
		$message = str_replace('[comment_permalink]', $comment_permalink, $message);
		$message = str_replace('[comment_author]', $comment->comment_author, $message);
		$message = str_replace('[comment_content]', $comment->comment_content, $message);
		$message = str_replace('[manager_link]', $manager_link, $message);

		// QTranslate support
		if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){
			$subject = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($subject);
			$message = str_replace('[post_title]', qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($post->post_title), $message);
			$message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
		}
		else{
			$message = str_replace('[post_title]', $post->post_title, $message);
		}
		if($content_type == 'text/html') $message = $this->wrap_html_message($message, $subject);

		wp_mail($clean_email, $subject, $message, $headers);
	}
	// end notify_user

	/**
	 * Generate a unique key to allow users to manage their subscriptions
	 */
	public function generate_key($_email){
		$day = date_i18n('Ymd');

		return md5($day.$this->salt.$_email);
	}
	// end generate_key

	/**
	 * Creates the HTML structure to properly handle HTML messages
	 */
	public function wrap_html_message($_message = '', $_subject = ''){
		return "<html><head><title>$_subject</title></head><body>$_message</body></html>";
	}
	// end _wrap_html_message

	/**
	 * Returns an email address where some possible 'offending' strings have been removed
	 */
	public function clean_email($_email){
		$offending_strings = array(
			"/to\:/i",
			"/from\:/i",
			"/bcc\:/i",
			"/cc\:/i",
			"/content\-transfer\-encoding\:/i",
			"/content\-type\:/i",
			"/mime\-version\:/i"
		);
		return esc_attr(stripslashes(strip_tags(preg_replace($offending_strings, '', $_email))));
	}
	// end clean_email

	/**
	 * Adds a new entry in the admin menu, to manage this plugin's options
	 */
	public function add_config_menu( $_s ) {
		global $current_user;

		if (current_user_can('manage_options')){
			add_options_page('Subscribe to Comments', 'Subscribe to Comments', 'manage_options', WP_PLUGIN_DIR.'/send-email-only-on-reply-to-my-comment/options/index.php');
		}
		return $_s;
	}
	// end add_config_menu

	/**
	 * Adds a custom stylesheet file to the admin interface
	 */
	public function add_options_stylesheet(){
		// It looks like WP_PLUGIN_URL doesn't honor the HTTPS setting in wp-config.php
		$stylesheet_url = (is_ssl()?str_replace('http://', 'https://', WP_PLUGIN_URL):WP_PLUGIN_URL).'/send-email-only-on-reply-to-my-comment/style.css';
		wp_register_style('subscribe-to-comments', $stylesheet_url);
		wp_enqueue_style('subscribe-to-comments');
	}
	public function add_post_comments_stylesheet(){
		// It looks like WP_PLUGIN_URL doesn't honor the HTTPS setting in wp-config.php
		$stylesheet_url = (is_ssl()?str_replace('http://', 'https://', WP_PLUGIN_URL):WP_PLUGIN_URL).'/send-email-only-on-reply-to-my-comment/post-and-comments.css';
		wp_register_style('subscribe-to-comments', $stylesheet_url);
		wp_enqueue_style('subscribe-to-comments');
	}
	// end add_stylesheet

	/**
	 * Adds custom HTML code to the HEAD section of the management page
	 */
	public function add_custom_header_meta(){
		echo html_entity_decode(stripslashes(get_option('subscribe_reloaded_custom_header_meta', '')), ENT_COMPAT, 'UTF-8');
	}
	// end add_custom_header_meta

	/**
	 * Adds a new column header to the Edit Comments panel
	 */
	public function add_column_header($_columns){
		$image_url = (is_ssl()?str_replace('http://', 'https://', WP_PLUGIN_URL):WP_PLUGIN_URL).'/send-email-only-on-reply-to-my-comment/images';
		$_columns['subscribe-reloaded'] = "<img src='$image_url/subscribe-to-comments-small.png' width='17' height='12' alt='Subscriptions' />";
		return $_columns;
	}
	// end add_comment_column_header

	/**
	 * Adds a new column to the Edit Comments panel
	 */
	public function add_comment_column($_column_name){
		if ('subscribe-reloaded' != $_column_name) return;

		global $comment;
		$subscription = $this->get_subscriptions(array('post_id','email'), array('equals','equals'), array($comment->comment_post_ID, $comment->comment_author_email), 'dt', 'DESC', 0, 1);
		if (count($subscription) == 0)
			_e('No','subscribe-reloaded');
		else
			echo '<a href="options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&subscribepanel=1&amp;srf=email&amp;srt=equals&amp;srv='.urlencode($comment->comment_author_email).'">'.$subscription[0]->status.'</a>';
	}
	// end add_column
	
	/**
	 * Adds a new column to the Posts management panel
	 */
	public function add_post_column($_column_name){
		if ('subscribe-reloaded' != $_column_name) return;

		global $post;
		load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/send-email-only-on-reply-to-my-comment/langs', '/send-email-only-on-reply-to-my-comment/langs');
		echo '<a href="options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&subscribepanel=1&amp;srf=post_id&amp;srt=equals&amp;srv='.$post->ID.'">'.count($this->get_subscriptions('post_id', 'equals', $post->ID)).'</a>';
	}
	// end add_column

	/**
	 * Contextual help (link to the support forum)
	 */
	public function contextual_help($contextual_help, $screen_id, $screen){
		if ($screen_id == 'send-email-only-on-reply-to-my-comment/options/index'){
			load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/send-email-only-on-reply-to-my-comment/langs', '/send-email-only-on-reply-to-my-comment/langs');
			$contextual_help = __('Need help on how to use Subscribe to Comments Reloaded? Visit the official','subscribe-reloaded').' <a href="http://wordpress.org/plugins/send-email-only-on-reply-to-my-comment/" target="_blank">'.__('support forum','subscribe-reloaded').'</a>. ';
			$contextual_help .= __('Feeling generous?','subscribe-reloaded').' '.__('Donate a few bucks!','subscribe-reloaded').'</a>';
		}
		return $contextual_help;
	}
	// end contextual_help

	/**
	 * Returns the URL of the management page as a shortcode
	 */
	public function subscribe_url_shortcode(){
		global $post;
		$user_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '');
		if (function_exists('qtrans_convertURL'))
			$user_link = qtrans_convertURL($user_link);
		if (strpos($user_link, '?') !== false)
			return "$user_link&amp;srp=$post->ID&amp;sra=s";
		else
			return "$user_link?srp=$post->ID&amp;sra=s";
	}
	// end subscribe_url_shortcode

	/**
	 * Copies the information from the stand-alone table to WP's core table
	 */
	private function _update_db(){
		global $wpdb;
		$stcr_table = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}subscribe_reloaded'");

		// Perform the import only if the target table does not contain any subscriptions
		$count_postmeta_rows = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key LIKE '\_stcr@\_%'");
		if (!empty($stcr_table) && $count_postmeta_rows == 0){
			$wpdb->query("
				INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value)
					SELECT post_ID, CONCAT('_stcr@_', email), CONCAT(dt, '|Y')
					FROM {$wpdb->prefix}subscribe_reloaded
					WHERE email LIKE '%@%.%' AND status = 'Y'
					GROUP BY email, post_ID");
		}
	}
	// end _update_db

	/**
	 * Retrieves the comment information from the databse
	 */
	private function _get_comment_object($_comment_ID){
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("
			SELECT comment_post_ID, comment_author_email, comment_approved, comment_type, comment_parent
			FROM $wpdb->comments
			WHERE comment_ID = %d LIMIT 1", $_comment_ID), OBJECT);
	}
	// end _get_comment_object

	/**
	 * Checks if a key is valid for a given email address
	 */
	private function _is_valid_key($_key, $_email){
		return ($this->generate_key($_email) == $_key);
	}
	// end _is_valid_key
}
// end of class declaration

// Bootstrap the whole thing
$wp_subscribe_reloaded = new wp_subscribe_reloaded();

// Set a cookie if the user just subscribed without commenting
$subscribe_to_comments_action = !empty($_POST['sra'])?$_POST['sra']:(!empty($_GET['sra'])?$_GET['sra']:0);
$subscribe_to_comments_post_ID = !empty($_POST['srp'])?intval($_POST['srp']):(!empty($_GET['srp'])?intval($_GET['srp']):0);
if (!empty($subscribe_to_comments_action) && !empty($_POST['subscribe_reloaded_email']) && ($subscribe_to_comments_action == 's') && ($subscribe_to_comments_post_ID > 0)){
	$subscribe_to_comments_clean_email = $wp_subscribe_reloaded->clean_email($_POST['subscribe_reloaded_email']);
	setcookie('comment_author_email'.COOKIEHASH, $subscribe_to_comments_clean_email, time()+1209600, '/');
}