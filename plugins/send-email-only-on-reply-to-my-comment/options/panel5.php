<?php
// Avoid direct access to this piece of code
if (!function_exists('is_admin') || !is_admin()){
	header('Location: /');
	exit;
}

// Update options
if (isset($_POST['options'])){
	$faulty_fields = '';
	if (isset($_POST['options']['purge_days']) && !subscribe_reloaded_update_option('purge_days', $_POST['options']['purge_days'], 'integer')) $faulty_fields = __('Autopurge requests','subscribe-reloaded').', ';
	if (isset($_POST['options']['enable_double_check']) && !subscribe_reloaded_update_option('enable_double_check', $_POST['options']['enable_double_check'], 'yesno')) $faulty_fields = __('Enable double check','subscribe-reloaded').', ';
	if (isset($_POST['options']['notify_authors']) && !subscribe_reloaded_update_option('notify_authors', $_POST['options']['notify_authors'], 'yesno')) $faulty_fields = __('Subscribe authors','subscribe-reloaded').', ';
	if (isset($_POST['options']['enable_html_emails']) && !subscribe_reloaded_update_option('enable_html_emails', $_POST['options']['enable_html_emails'], 'yesno')) $faulty_fields = __('Enable HTML emails','subscribe-reloaded').', ';
	if (isset($_POST['options']['process_trackbacks']) && !subscribe_reloaded_update_option('process_trackbacks', $_POST['options']['process_trackbacks'], 'yesno')) $faulty_fields = __('Send trackbacks','subscribe-reloaded').', ';
	if (isset($_POST['options']['enable_admin_messages']) && !subscribe_reloaded_update_option('enable_admin_messages', $_POST['options']['enable_admin_messages'], 'yesno')) $faulty_fields = __('Notify admin','subscribe-reloaded').', ';
	if (isset($_POST['options']['admin_subscribe']) && !subscribe_reloaded_update_option('admin_subscribe', $_POST['options']['admin_subscribe'], 'yesno')) $faulty_fields = __('Let admin subscribe','subscribe-reloaded').', ';

	// Display an alert in the admin interface if something went wrong
	echo '<div class="updated fade"><p>';
	if (empty($faulty_fields)){
			_e('Your settings have been successfully updated.','subscribe-reloaded');
	}
	else{
		_e('There was an error updating the following fields:','subscribe-reloaded');
		echo ' <strong>'.substr($faulty_fields,0,-2).'</strong>';
	}
	echo "</p></div>\n";
}
wp_print_scripts( 'quicktags' );
?>
<form action="admin.php?page=send-email-only-on-reply-to-my-comment/options/index.php&subscribepanel=<?php echo $current_panel ?>" method="post">
<table class="form-table <?php echo $wp_locale->text_direction ?>">
	<tr>
		<th scope="row"><label for="purge_days"><?php _e('Autopurge requests','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[purge_days]" id="purge_days" value="<?php echo subscribe_reloaded_get_option('purge_days'); ?>" size="10"> <?php _e('days','subscribe-reloaded') ?>
			<div class="description"><?php _e("Delete pending subscriptions (not confirmed) after X days. Zero disables this feature.",'subscribe-reloaded'); ?></div></td>
	</tr>
	
</tbody>
</table>
<p class="submit"><input type="submit" value="<?php _e('Save Changes') ?>" class="button-primary" name="Submit"></p>
</form>
