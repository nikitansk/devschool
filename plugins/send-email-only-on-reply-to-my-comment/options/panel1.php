<?php
// Avoid direct access to this piece of code
if (!function_exists('is_admin') || !is_admin()){
	header('Location: /');
	exit;
}
$action = !empty($_POST['sra'])?$_POST['sra']:(!empty($_GET['sra'])?$_GET['sra']:'');
if ($action == 'edit-subscription'){
	require_once(WP_PLUGIN_DIR.'/send-email-only-on-reply-to-my-comment/options/panel1-edit-subscription.php');
	return;
}
if (is_readable(WP_PLUGIN_DIR."/send-email-only-on-reply-to-my-comment/options/panel1-business-logic.php"))
	require_once(WP_PLUGIN_DIR.'/send-email-only-on-reply-to-my-comment/options/panel1-business-logic.php');
?>

<div class="postbox">
<p class="subscribe-list-navigation"><?php echo "$previous_link $next_link" ?>
</p>
<h3><?php _e('Search subscriptions','subscribe-reloaded') ?></h3>
<form action="options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&amp;subscribepanel=1" method="post" id="subscription_form" name="subscription_form"
	onsubmit="if(this.sra[0].checked) return confirm('<?php _e('Please remember: this operation cannot be undone. Are you sure you want to proceed?', 'subscribe-reloaded') ?>')">
<fieldset style="border:0">
<?php
	if (!empty($subscriptions) && is_array($subscriptions)){
		$order_post_id = "<a style='text-decoration:none' title='".__('Reverse the order by Post ID','subscribe-reloaded')."' href='options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&amp;subscribepanel=1&amp;srv=".urlencode($search_value)."&amp;srt=".urlencode($operator)."&amp;srob=post_id&amp;sro=".(($order=='ASC')?"DESC'>&or;":"ASC'>&and;")."</a>";
		$order_dt = "<a style='text-decoration:none' title='".__('Reverse the order by Date/Time','subscribe-reloaded')."' href='options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&amp;subscribepanel=1&amp;srv=".urlencode($search_value)."&amp;srt=".urlencode($operator)."&amp;srob=dt&amp;sro=".(($order=='ASC')?"DESC'>&or;":"ASC'>&and;")."</a>";
		$order_status = "<a style='text-decoration:none' title='".__('Reverse the order by Date/Time','subscribe-reloaded')."' href='options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&amp;subscribepanel=1&amp;srv=".urlencode($search_value)."&amp;srt=".urlencode($operator)."&amp;srob=status&amp;sro=".(($order=='ASC')?"DESC'>&or;":"ASC'>&and;")."</a>";

		$show_post_column = ($operator != 'equals' || $search_field != 'post_id')?"<span class='subscribe-column subscribe-column-1'>".__('Post (ID)','subscribe-reloaded')."&nbsp;&nbsp;$order_post_id</span>":'';
		$show_email_column = ($operator != 'equals' || $search_field != 'email')?"<span class='subscribe-column subscribe-column-2'>".__('Email','subscribe-reloaded')."</span>":'';

		echo '<p>'.__('','subscribe-reloaded')."  ".__('Rows:','subscribe-reloaded').' '.($offset+1)." - $ending_to ".__('of','subscribe-reloaded')." $count_total</p>";
		echo '<p>'.__('Y = Receive Comment Reply Notification Only, R = Receive Notification For All New Comments, C = inactive','subscribe-reloaded').'</p>';
		echo '<ul>';

		echo "<li class='subscribe-list-header'>
				<input class='checkbox' type='checkbox' name='subscription_list_select_all' id='stcr_select_all' 
					onchange='t=document.forms[\"subscription_form\"].elements[\"subscriptions_list[]\"];c=t.length;if(!c){t.checked=this.checked}else{for(var i=0;i<c;i++){t[i].checked=!t[i].checked}}'/>
				<span class='subscribe-column' style='width:38px'>&nbsp;</span>
				$show_post_column
				$show_email_column
				<span class='subscribe-column subscribe-column-3'>".__('Date and Time','subscribe-reloaded')." &nbsp;&nbsp;$order_dt</span>
				<span class='subscribe-column subscribe-column-4'>".__('Status','subscribe-reloaded')." &nbsp;&nbsp;$order_status</span></li>\n";
		$alternate = '';
		$date_time_format = get_option('date_format').' '.get_option('time_format');
		foreach($subscriptions as $a_subscription){
			$title = get_the_title($a_subscription->post_id);
			$title = (strlen($title) > 35)?substr($title, 0, 35).'..':$title;
			$row_post = ($operator != 'equals' || $search_field != 'post_id')?"<a class='subscribe-column subscribe-column-1' href='options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&amp;subscribepanel=1&amp;srf=post_id&amp;srt=equals&amp;srv=$a_subscription->post_id'>$title ($a_subscription->post_id)</a> ":'';
			$row_email = ($operator != 'equals' || $search_field != 'email')?"<span class='subscribe-column subscribe-column-2'><a href='options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&subscribepanel=1&amp;srf=email&amp;srt=equals&amp;srv=".urlencode($a_subscription->email)."'>$a_subscription->email</a></span> ":'';
			$date_time = date_i18n($date_time_format, strtotime($a_subscription->dt));
			$alternate = ($alternate==' class="row"')?' class="row alternate"':' class="row"';
			echo "<li$alternate>
					<label for='sub_{$a_subscription->meta_id}' class='hidden'>".__('Subscription','subscribe-reloaded')." {$a_subscription->meta_id}</label>
					<input class='checkbox' type='checkbox' name='subscriptions_list[]' value='$a_subscription->post_id,".urlencode($a_subscription->email)."' id='sub_{$a_subscription->meta_id}' />
					<a class='subscribe-column' href='options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&amp;subscribepanel=1&amp;sra=edit-subscription&amp;srp=".$a_subscription->post_id."&amp;sre=".urlencode($a_subscription->email)."'><img src='".WP_PLUGIN_URL."/send-email-only-on-reply-to-my-comment/images/edit.png' alt='".__('Edit','subscribe-reloaded')."' width='16' height='16' /></a>
					<a class='subscribe-column' href='options-general.php?page=send-email-only-on-reply-to-my-comment/options/index.php&amp;subscribepanel=1&amp;sra=delete-subscription&amp;srp=".$a_subscription->post_id."&amp;sre=".urlencode($a_subscription->email)."' onclick='return confirm(\"".__('Please remember: this operation cannot be undone. Are you sure you want to proceed?', 'subscribe-reloaded')."\");'><img src='".WP_PLUGIN_URL."/send-email-only-on-reply-to-my-comment/images/delete.png' alt='".__('Delete','subscribe-reloaded')."' width='16' height='16' /></a>
					$row_post
					$row_email
					<span class='subscribe-column subscribe-column-3'>$date_time</span>
					<span class='subscribe-column subscribe-column-4'>$a_subscription->status</span>
					</li>\n";
		}
		echo '</ul>';
		echo '<p>'.__('Action:','subscribe-reloaded').'
				<input type="radio" name="sra" value="delete" id="action_type_delete" /> <label for="action_type_delete">'.__('Delete','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="sra" value="suspend" id="action_type_suspend" checked="checked" /> <label for="action_type_suspend">'.__('Suspend','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="sra" value="force_y" id="action_type_force_y" /> <label for="action_type_force_y">'.__('Activate and set to Y','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="sra" value="force_r" id="action_type_force_r" /> <label for="action_type_force_r">'.__('Activate and set to R','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp;
				</p>';
		echo '<p><input type="submit" class="subscribe-form-button" value="'.__('Update subscriptions','subscribe-reloaded').'" /></p>';
		echo "<input type='hidden' name='srf' value='$search_field'/><input type='hidden' name='srt' value='$operator'/><input type='hidden' name='srv' value='$search_value'/><input type='hidden' name='srsf' value='$offset'/><input type='hidden' name='srrp' value='$limit_results'/><input type='hidden' name='srob' value='$order_by'/><input type='hidden' name='sro' value='$order'/>";
	}
	elseif ($action == 'search')
		echo '<p>'.__('Sorry, no subscriptions match your search criteria.','subscribe-reloaded')."</p>";
?>
</fieldset>
</form>
</div>