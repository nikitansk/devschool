<?php
// Avoid direct access to this piece of code
if (!function_exists('is_admin') || !is_admin()){
	header('Location: /');
	exit;
}

// Update options
if (isset($_POST['options'])){
	$faulty_fields = '';
	if (isset($_POST['options']['show_subscription_box']) && !subscribe_reloaded_update_option('show_subscription_box', $_POST['options']['show_subscription_box'], 'yesno')) $faulty_fields = __('Enable default checkbox','subscribe-reloaded').', ';
	if (isset($_POST['options']['show_subscription_box1']) && !subscribe_reloaded_update_option('show_subscription_box1', $_POST['options']['show_subscription_box1'], 'yesno')) $faulty_fields = __('Enable default checkbox1','subscribe-reloaded').', ';
	if (isset($_POST['options']['checked_by_default']) && !subscribe_reloaded_update_option('checked_by_default', $_POST['options']['checked_by_default'], '123')) $faulty_fields = __('Checked by default','subscribe-reloaded').', ';	
	if (isset($_POST['options']['checkbox_html']) && !subscribe_reloaded_update_option('checkbox_html', $_POST['options']['checkbox_html'], 'text-no-encode')) $faulty_fields = __('Custom HTML','subscribe-reloaded').', ';
	if (isset($_POST['options']['sub_options']) && !subscribe_reloaded_update_option('sub_options', $_POST['options']['sub_options'], 'text-no-encode')) $faulty_fields = __('Custom HTML','subscribe-reloaded').', ';
	if (isset($_POST['options']['checkbox_label']) && !subscribe_reloaded_update_option('checkbox_label', $_POST['options']['checkbox_label'], 'text')) $faulty_fields = __('Checkbox label','subscribe-reloaded').', ';
	if (isset($_POST['options']['dropdown_label']) && !subscribe_reloaded_update_option('dropdown_label', $_POST['options']['dropdown_label'], 'text')) $faulty_fields = __('dropdown label','subscribe-reloaded').', ';
	if (isset($_POST['options']['dropdown_label1']) && !subscribe_reloaded_update_option('dropdown_label1', $_POST['options']['dropdown_label1'], 'text')) $faulty_fields = __('dropdown label1','subscribe-reloaded').', ';
	if (isset($_POST['options']['dropdown_label2']) && !subscribe_reloaded_update_option('dropdown_label2', $_POST['options']['dropdown_label2'], 'text')) $faulty_fields = __('dropdown label2','subscribe-reloaded').', ';
	if (isset($_POST['options']['subscribed_label']) && !subscribe_reloaded_update_option('subscribed_label', $_POST['options']['subscribed_label'], 'text')) $faulty_fields = __('Subscribed label','subscribe-reloaded').', ';
	if (isset($_POST['options']['subscribed_waiting_label']) && !subscribe_reloaded_update_option('subscribed_waiting_label', $_POST['options']['subscribed_waiting_label'], 'text')) $faulty_fields = __('Awaiting label','subscribe-reloaded').', ';
	if (isset($_POST['options']['author_label']) && !subscribe_reloaded_update_option('author_label', $_POST['options']['author_label'], 'text')) $faulty_fields = __('Author label','subscribe-reloaded').', ';

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
?>
<form action="admin.php?page=send-email-only-on-reply-to-my-comment/options/index.php&subscribepanel=<?php echo $current_panel ?>" method="post">
<h3><?php _e('Options','subscribe-reloaded') ?></h3>
<table class="form-table <?php echo $wp_locale->text_direction ?>">
<tbody>
	<tr>
		<th scope="row"><label for="show_subscription_box"><?php _e('Enable Plugin?','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[show_subscription_box]" id="show_subscription_box" value="yes"<?php echo (subscribe_reloaded_get_option('show_subscription_box') == 'yes')?' checked="checked"':''; ?>> <?php _e('Yes','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[show_subscription_box]" value="no" <?php echo (subscribe_reloaded_get_option('show_subscription_box') == 'no')?'  checked="checked"':''; ?>> <?php _e('No','subscribe-reloaded') ?>
			<div class="description"><?php _e('Enable or Disable Plugin.','subscribe-reloaded'); ?></div></td>
	</tr>
    	<tr>
		<th scope="row"><label for="checked_by_default"><?php _e('Choose The Default Label','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[checked_by_default]" id="checked_by_default" value="1"<?php echo (subscribe_reloaded_get_option('checked_by_default') == '1')?' checked="checked"':''; ?>> <?php echo get_option('subscribe_reloaded_dropdown_label', 'Do Not Send Email Notifications.') ?> &nbsp; &nbsp; &nbsp;<br />
			<input type="radio" name="options[checked_by_default]" value="2" <?php echo (subscribe_reloaded_get_option('checked_by_default') == '2')?'  checked="checked"':''; ?>> <?php echo get_option('subscribe_reloaded_dropdown_label1', 'Send Email Notification ONLY If Someone Replies To My Comment(s).') ?>&nbsp; &nbsp; &nbsp;<br />
			<input type="radio" name="options[checked_by_default]" value="3" <?php echo (subscribe_reloaded_get_option('checked_by_default') == '3')?'  checked="checked"':''; ?>> <?php echo get_option('subscribe_reloaded_dropdown_label2', 'Send Email Notification Whenever A New Comment Is Posted.') ?>
			<div class="description"><?php _e('Decide Which Label Should Be Selected By Default.','subscribe-reloaded'); ?></div></td>
	</tr>
    <tr>
		<th scope="row"><label for="show_subscription_box1"><?php _e('Show Message With Dropdown Menu?','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[show_subscription_box1]" id="show_subscription_box1" value="yes"<?php echo (subscribe_reloaded_get_option('show_subscription_box1') == 'yes')?' checked="checked"':''; ?>> <?php _e('Yes','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[show_subscription_box1]" value="no" <?php echo (subscribe_reloaded_get_option('show_subscription_box1') == 'no')?'  checked="checked"':''; ?>> <?php _e('No','subscribe-reloaded') ?>
			<div class="description"><?php _e('Do You Want To Show A Message With The Dropdown Menu?.','subscribe-reloaded'); ?></div></td>
	</tr>
    <tr>
		<th scope="row"><label for="checkbox_html"><?php _e('Show Message Above or Below The Dropdown Menu?','subscribe-reloaded') ?></label></th>
        <td>
			<input type="radio" name="options[checkbox_html]" id="checkbox_html" value="<p><label for='subscribe-reloaded'> [checkbox_label][checkbox_field]</label></p>"<?php echo (subscribe_reloaded_get_option('checkbox_html') == "<p><label for='subscribe-reloaded'> [checkbox_label][checkbox_field]</label></p>")?' checked="checked"':''; ?>> <?php _e('Above','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[checkbox_html]" value="<p><label for='subscribe-reloaded'> [checkbox_field][checkbox_label]</label></p>" <?php echo (subscribe_reloaded_get_option('checkbox_html') == "<p><label for='subscribe-reloaded'> [checkbox_field][checkbox_label]</label></p>")?'  checked="checked"':''; ?>> <?php _e('Below','subscribe-reloaded') ?>
			<div class="description"><?php _e('Do You Want To Show The Message Above or Below The Dropdown Menu?.','subscribe-reloaded'); ?></div></td>
        </tr>
     <tr>
		<th scope="row"><label for="sub_options"><?php _e('Where do you want to show the Drop-Down Menu?','subscribe-reloaded') ?></label></th>
        <td>
			<input type="radio" name="options[sub_options]" id="sub_options" value="op1"<?php echo (subscribe_reloaded_get_option('sub_options') == "op1")?' checked="checked"':''; ?>> <?php _e('Below Comment-Submit Button','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[sub_options]" value="op2" <?php echo (subscribe_reloaded_get_option('sub_options') == "op2")?'  checked="checked"':''; ?>> <?php _e('Above Comment Text Box','subscribe-reloaded') ?>
			<div class="description"><?php _e('Do You Want To Show The drop-down menu [subscription-options] below the Submit-Comment button or above comment Text Box?','subscribe-reloaded'); ?></div></td>
            </tr>
     </tbody>
</table>

<h3><?php _e('Messages for your visitors','subscribe-reloaded') ?></h3>
<table class="form-table <?php echo $wp_locale->text_direction ?>">
<tbody>
	<tr>
		<th scope="row"><label for="checkbox_label"><?php _e('Message To Show','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[checkbox_label]" id="checkbox_label" value="<?php echo subscribe_reloaded_get_option('checkbox_label'); ?>" size="70">
			<div class="description"><?php _e('Message That Will Show With The Dropdown Menu','subscribe-reloaded'); ?></div></td>
	</tr>
    
    <tr>
		<th scope="row"><label for="dropdown_label"><?php _e('Label 1','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[dropdown_label]" id="dropdown_label" value="<?php echo subscribe_reloaded_get_option('dropdown_label'); ?>" size="70">
			<div class="description"><?php _e('Label 1 associated to the dropdown menu.','subscribe-reloaded'); ?></div></td>
	</tr>
    
     <tr>
		<th scope="row"><label for="dropdown_label1"><?php _e('Label 2','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[dropdown_label1]" id="dropdown_label1" value="<?php echo subscribe_reloaded_get_option('dropdown_label1'); ?>" size="70">
			<div class="description"><?php _e('Label 2 associated to the dropdown menu.','subscribe-reloaded'); ?></div></td>
	</tr>
    
     <tr>
		<th scope="row"><label for="dropdown_label2"><?php _e('Label 3','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[dropdown_label2]" id="dropdown_label2" value="<?php echo subscribe_reloaded_get_option('dropdown_label2'); ?>" size="70">
			<div class="description"><?php _e('Label 3 associated to the dropdown menu.','subscribe-reloaded'); ?></div></td>
	</tr>
    
	<tr>
		<th scope="row"><label for="subscribed_label"><?php _e('Subscribed label','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[subscribed_label]" id="subscribed_label" value="<?php echo subscribe_reloaded_get_option('subscribed_label'); ?>" size="70">
			<div class="description"><?php _e('Label shown to those who are already subscribed to a post. Allowed tag: [manager_link]','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="subscribed_waiting_label"><?php _e('Pending label','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[subscribed_waiting_label]" id="subscribed_waiting_label" value="<?php echo subscribe_reloaded_get_option('subscribed_waiting_label'); ?>" size="70">
			<div class="description"><?php _e("Label shown to those who are already subscribed, but haven't clicked on the confirmation link yet. Allowed tag: [manager_link]",'subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="author_label"><?php _e('Author label','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[author_label]" id="author_label" value="<?php echo subscribe_reloaded_get_option('author_label'); ?>" size="70">
			<div class="description"><?php _e('Label shown to authors (and administrators). Allowed tag: [manager_link]','subscribe-reloaded'); ?></div></td>
	</tr>
</tbody>
</table>
<p class="submit"><input type="submit" value="<?php _e('Save Changes') ?>" class="button-primary" name="Submit"></p>
</form>