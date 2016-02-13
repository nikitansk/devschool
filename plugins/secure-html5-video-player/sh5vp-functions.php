<?php 

if (!class_exists('SH5VP_S3')) require_once 's3/S3.php';

$secure_html5_video_player_s3 = NULL;



if ( !function_exists('secure_html5_video_player_s3_link_expire_seconds') ):
function secure_html5_video_player_s3_link_expire_seconds() {
	$transient_key = 'sh5vp:s3:link_expire';
	$exists = secure_html5_video_player_get_transient($transient_key);
	if ($exists !== FALSE) {
		return $exists;
	}
	$retval = 28800;
	$secure_html5_video_player_s3_link_expire = get_option('secure_html5_video_player_s3_link_expire');
	$secure_html5_video_player_s3_link_expire_units = get_option('secure_html5_video_player_s3_link_expire_units');
	if (!$secure_html5_video_player_s3_link_expire) {
		$secure_html5_video_player_s3_link_expire = 28800;
	}
	else {
		$secure_html5_video_player_s3_link_expire = floatval($secure_html5_video_player_s3_link_expire);
	}
	if (!$secure_html5_video_player_s3_link_expire_units) {
		$secure_html5_video_player_s3_link_expire_units = 'seconds';
	}
	if ($secure_html5_video_player_s3_link_expire_units == 'hours') {
		$retval = $secure_html5_video_player_s3_link_expire * 3600;
	}
	else if ($secure_html5_video_player_s3_link_expire_units == 'days') {
		$retval = $secure_html5_video_player_s3_link_expire * 86400;
	}
	else if ($secure_html5_video_player_s3_link_expire_units == 'minutes') {
		$retval = $secure_html5_video_player_s3_link_expire * 60;
	}
	else {
		$retval = $secure_html5_video_player_s3_link_expire;
	}
	$retval = intval(abs(round($retval)));
	secure_html5_video_player_set_transient($transient_key, $retval);
	return $retval;
}
endif;



if ( !function_exists('secure_html5_video_player_options_form_s3') ):
function secure_html5_video_player_options_form_s3() {
	$secure_html5_video_player_enable_s3 = ('yes' == get_option('secure_html5_video_player_enable_s3') ? 'checked="checked"' : '');
	$secure_html5_video_player_s3_access_key = get_option('secure_html5_video_player_s3_access_key');
	$secure_html5_video_player_s3_secret_key = get_option('secure_html5_video_player_s3_secret_key');
	$secure_html5_video_player_s3_server = get_option('secure_html5_video_player_s3_server');
	$secure_html5_video_player_s3_bucket = get_option('secure_html5_video_player_s3_bucket');
	$video_dir = secure_html5_video_player_s3_video_dir();
	$secure_html5_video_player_s3_link_expire = get_option('secure_html5_video_player_s3_link_expire');
	$secure_html5_video_player_s3_link_expire_units = get_option('secure_html5_video_player_s3_link_expire_units');
	if (!$secure_html5_video_player_s3_link_expire) $secure_html5_video_player_s3_link_expire = 8;
	if (!$secure_html5_video_player_s3_link_expire_units) $secure_html5_video_player_s3_link_expire_units = 'hours';
	
	$s3_servers = array(
		's3.amazonaws.com', 'Amazon S3: US Standard',
		's3-eu-west-1.amazonaws.com', 'Amazon S3: Ireland',
		's3-us-west-1.amazonaws.com', 'Amazon S3: Northern California',
		's3-us-west-2.amazonaws.com', 'Amazon S3: Oregon',
		's3-sa-east-1.amazonaws.com', 'Amazon S3: Sau Paulo',
		's3-ap-southeast-1.amazonaws.com', 'Amazon S3: Singapore',
		's3-ap-southeast-2.amazonaws.com', 'Amazon S3: Sydney',
		's3-ap-northeast-1.amazonaws.com', 'Amazon S3: Tokyo',
		'objects.dreamhost.com', 'DreamObjects',
		'other', 'Other:'
	);
	$s3_time_units = array(
		'days', 'hours', 'minutes', 'seconds'
	);
	?>
	<input type='checkbox' value="yes" id="secure_html5_video_player_enable_s3" name='secure_html5_video_player_enable_s3' <?php print $secure_html5_video_player_enable_s3 ?> />
	<label class="title" for='secure_html5_video_player_enable_s3'><?php _e('Enable Simple Storage Service', 'secure-html5-video-player'); ?></label>
	<br/>
	<small><?php _e('If checked, media is permitted to be loaded from the specified S3 service (<a href="http://aws.amazon.com/s3/" target="_blank">Amazon S3</a>, <a href="http://dreamhost.com/cloud/dreamobjects/" target="_blank">DreamObjects</a>, ...). ', 'secure-html5-video-player'); ?></small><br/><br/>

	<label class="title" for='secure_html5_video_player_s3_server'><?php _e('S3 Server', 'secure-html5-video-player'); ?></label><br/>
	<select id='secure_html5_video_player_s3_server' name='secure_html5_video_player_s3_server' onchange='
		var server_sel = jQuery("#secure_html5_video_player_s3_server");
		var server_other = jQuery("#secure_html5_video_player_s3_server_other");
		if (server_sel.val() == "other") {
			server_other.css({
				"display":"inline"
			});
		}
		else {
			server_other.css({
				"display":"none"
			});
			server_other.val(server_sel.val());
		}
	'>
		<?php 
			$count_s3_servers = count($s3_servers);
			$found_sel_server = FALSE;
			$is_other_server = FALSE;
			for ($i = 0; $i < $count_s3_servers; $i += 2) {
				$sel = '';
				$is_other_server = ($s3_servers[$i] == 'other' && !$found_sel_server);
				if ($secure_html5_video_player_s3_server == $s3_servers[$i] || $is_other_server) {
					$sel = ' selected="selected" ';
					$found_sel_server = TRUE;
				}
				?><option <?php echo $sel; ?> value="<?php echo $s3_servers[$i]; ?>"><?php 
					echo $s3_servers[$i+1];
					if ($s3_servers[$i] != 'other') {
						echo ' (';
						echo $s3_servers[$i];
						echo ')';
					}
				?></option><?php
			}
		?>
	</select><input type='text' size='50' name='secure_html5_video_player_s3_server_other' id='secure_html5_video_player_s3_server_other' value='<?php echo $secure_html5_video_player_s3_server; ?>' 
	<?php if (! $is_other_server) { ?>
		style="display:none;"
	<?php } ?>
	/><br/>
	<small><?php _e('The server selected muct match the region in which the bucket was created. If you do not see the correct region or server listed here, select [other] and input the correct S3 server address.', 'secure-html5-video-player'); ?></small><br/><br/>

	<label class="title" for='secure_html5_video_player_s3_access_key'><?php _e('Access Key', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_s3_access_key" name='secure_html5_video_player_s3_access_key'  size='50' value='<?php echo $secure_html5_video_player_s3_access_key ?>' /><br/><br/>

	<label class="title" for='secure_html5_video_player_s3_secret_key'><?php _e('Secret Key', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_s3_secret_key" name='secure_html5_video_player_s3_secret_key'  size='50' value='<?php echo $secure_html5_video_player_s3_secret_key ?>' /><br/><br/>
	
	<label class="title" for='secure_html5_video_player_s3_bucket'><?php _e('S3 Bucket', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_s3_bucket" name='secure_html5_video_player_s3_bucket'  size='50' value='<?php echo $secure_html5_video_player_s3_bucket ?>' /><br/>
	<small><?php _e('The bucket must reside in the S3 server previously specified. ', 'secure-html5-video-player'); ?></small><br/><br/>

	<label class="title" for='secure_html5_video_player_s3_video_dir'><?php _e('S3 Video Directory', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_s3_video_dir" name='secure_html5_video_player_s3_video_dir'  size='50' value='<?php echo $video_dir ?>' /><br/>
	<small><?php _e('The directory path in the bucket where the videos are stored. This directory should be made private if you wish to secure your videos. ', 'secure-html5-video-player'); ?></small><br/><br/>

	<label class="title" for='secure_html5_video_player_s3_link_expire'><?php _e('S3 Media Lifespan', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_s3_link_expire" name='secure_html5_video_player_s3_link_expire'  size='10' value='<?php echo $secure_html5_video_player_s3_link_expire ?>' /><select id='secure_html5_video_player_s3_link_expire_units' name='secure_html5_video_player_s3_link_expire_units'><?php 
			$count_s3_time_units = count($s3_time_units);
			for ($i = 0; $i < $count_s3_time_units; $i++) {
				$sel = '';
				if ($secure_html5_video_player_s3_link_expire_units == $s3_time_units[$i]) {
					$sel = ' selected="selected" ';
				}
				?><option <?php echo $sel; ?> value="<?php echo $s3_time_units[$i]; ?>"><?php echo $s3_time_units[$i]; ?></option><?php
			}
	?></select><br/>
	<small><?php _e('The amount of time that the visitor is granted access to the media served from S3.', 'secure-html5-video-player'); ?></small><br/><br/>
	<?php
}
endif;



if ( !function_exists('secure_html5_video_player_s3_video_dir') ):
function secure_html5_video_player_s3_video_dir() {
	$video_dir = get_option('secure_html5_video_player_s3_video_dir');
	if (secure_html5_video_player_startsWith($video_dir, '/')) {
		$video_dir = substr($video_dir, 1);
	}
	if (! secure_html5_video_player_endsWith($video_dir, '/')) {
		$video_dir .= '/';
	}
	return $video_dir;
}
endif;



if ( !function_exists('secure_html5_video_player_s3_object') ):
function secure_html5_video_player_s3_object() {
	global $secure_html5_video_player_s3;
	if ($secure_html5_video_player_s3 == NULL) {
		$secure_html5_video_player_s3_access_key = get_option('secure_html5_video_player_s3_access_key');
		$secure_html5_video_player_s3_secret_key = get_option('secure_html5_video_player_s3_secret_key');
		$secure_html5_video_player_s3_server = get_option('secure_html5_video_player_s3_server');
		$secure_html5_video_player_s3 = new SH5VP_S3(
			$secure_html5_video_player_s3_access_key, 
			$secure_html5_video_player_s3_secret_key, 
			TRUE, 
			$secure_html5_video_player_s3_server
		);
	}
	return $secure_html5_video_player_s3;
}
endif;



if ( !function_exists('secure_html5_video_player_is_s3_enabled') ):
function secure_html5_video_player_is_s3_enabled() {
	$transient_key = 'sh5vp:s3:enabled';
	$retval = secure_html5_video_player_get_transient($transient_key);
	if ($retval !== FALSE) {
		return $retval;
	}
	$retval = 0;
	if ('yes' == get_option('secure_html5_video_player_enable_s3')) {
		$retval = 1;
	}
	secure_html5_video_player_set_transient($transient_key, $retval);
	return $retval;
}
endif;



if ( !function_exists('secure_html5_video_player_s3_file_list') ):
function secure_html5_video_player_s3_file_list() {
	$transient_key = 'sh5vp:s3:list';
	$exists = secure_html5_video_player_get_transient($transient_key);
	if ($exists !== FALSE) {
		return $exists;
	}

	$video_dir = secure_html5_video_player_s3_video_dir();
	$secure_html5_video_player_s3 = secure_html5_video_player_s3_object();
	$secure_html5_video_player_s3_bucket = get_option('secure_html5_video_player_s3_bucket');
	$s3_video_files = $secure_html5_video_player_s3->getBucket(
		$secure_html5_video_player_s3_bucket, $video_dir, $video_dir
	);
	$s3_ary = array();
	foreach ($s3_video_files as $curr_s3_vid => $curr_s3_info) {
		$curr = substr($curr_s3_vid, strlen( $video_dir));
		$s3_ary[ secure_html5_video_player_filename_no_ext($curr) ] = array();
	}

	secure_html5_video_player_set_transient($transient_key, $s3_ary);
	return $s3_ary;
}
endif;



if ( !function_exists('secure_html5_video_player_s3_media_exists') ):
function secure_html5_video_player_s3_media_exists($filename) {
	$video_dir = secure_html5_video_player_s3_video_dir();
	$secure_html5_video_player_s3 = secure_html5_video_player_s3_object();
	$secure_html5_video_player_s3_bucket = get_option('secure_html5_video_player_s3_bucket');
	
	$filename_normalized_ext = secure_html5_video_player_filename_normalized_ext($filename);
	if (! secure_html5_video_player_is_s3_enabled()) return FALSE;

	$transient_key = 'sh5vp:s3:' . $filename_normalized_ext;
	$exists = secure_html5_video_player_get_transient($transient_key);
	if ($exists !== FALSE) {
		return $exists;
	}

	$filename_no_ext = secure_html5_video_player_filename_no_ext($filename);
	$prefix = $video_dir . $filename_no_ext . '.';

	$s3_video_files = $secure_html5_video_player_s3->getBucket(
		$secure_html5_video_player_s3_bucket, $prefix, $prefix
	);
	foreach ($s3_video_files as $curr_s3_vid => $curr_s3_info) {
		$curr = substr($curr_s3_vid, strlen( $video_dir));
		$link = $secure_html5_video_player_s3->getAuthenticatedURL(
			$secure_html5_video_player_s3_bucket, 
			$curr_s3_vid, 
			secure_html5_video_player_s3_link_expire_seconds(), 
			FALSE, // hostBucket
			TRUE // request https url
		);
		$curr_filename_normalized_ext = secure_html5_video_player_filename_normalized_ext($curr);
		$transient_key = 'sh5vp:s3:' . $curr_filename_normalized_ext;
		secure_html5_video_player_set_transient($transient_key, $link);
		if ($curr_filename_normalized_ext == $filename_normalized_ext) {
			$exists = $link;
		}
	}
	
	$ext_ary = array('mp4', 'ogv', 'webm', 'png', 'jpg', 'gif');
	foreach ($ext_ary as $ext) {
		$transient_key = 'sh5vp:s3:' . $filename_no_ext . '.' . $ext;
		$other_exists = secure_html5_video_player_get_transient($transient_key);
		if ($other_exists === FALSE) {
			secure_html5_video_player_set_transient($transient_key, 0);
		}
	}	
	return $exists;
}
endif;
add_filter('secure_html5_video_player_s3_media_exists', 'secure_html5_video_player_s3_media_exists', 1, 2);



if ( !function_exists('secure_html5_video_player_has_media_server') ):
function secure_html5_video_player_has_media_server() {
	$transient_key = 'sh5vp:has_media_server';
	$exists = secure_html5_video_player_get_transient($transient_key);
	if ($exists === FALSE) {
		$has_media_server = ('yes' == get_option('secure_html5_video_player_enable_media_server'));
		$server_list = secure_html5_video_player_media_server_address_list();
		$exists = ($has_media_server && count($server_list) > 0);
		secure_html5_video_player_set_transient($transient_key, $exists);
	}
	return $exists;
}
endif;



if ( !function_exists('secure_html5_video_player_plugin_action_links') ):
function secure_html5_video_player_plugin_action_links( $links, $file ) {
	array_unshift($links, '<a href="options-general.php?page=secure-html5-video-player">'.__('Settings').'</a>');
	return $links;
}
endif;



if ( !function_exists('secure_html5_video_player_get_transient') ):
function secure_html5_video_player_get_transient($transient_key) {
	if (function_exists('apc_fetch')) {
		$success = FALSE;
		$val = apc_fetch($transient_key, $success);
		if ($success) {
			return $val;
		}
		else {
			return FALSE;
		}
	}
	else {
		global $transient_ary;
		if (!isset($transient_ary)) $transient_ary = array();
		if (isset($transient_ary[$transient_key])) {
			return $transient_ary[$transient_key];
		}
	}
	return FALSE;
}
endif;



if ( !function_exists('secure_html5_video_player_set_transient') ):
function secure_html5_video_player_set_transient($transient_key, $val) {
	if ($val === FALSE) $val = 0;
	else if ($val === TRUE) $val = 1;
	if (function_exists('apc_add')) {
		global $secure_html5_video_player_cache_ttl;
		apc_add($transient_key, $val, $secure_html5_video_player_cache_ttl);
	}
	else {
		global $transient_ary;
		if (!isset($transient_ary)) $transient_ary = array();
		$transient_ary[$transient_key] = $val;
	}
}
endif;


if ( !function_exists('secure_html5_video_player_clear_transient') ):
function secure_html5_video_player_clear_transient() {
	if (function_exists('apc_clear_cache')) {
		global $secure_html5_video_player_cache_ttl;
		apc_clear_cache('user');
	}
	else {
		global $transient_ary;
		$transient_ary = array();
	}
}
endif;



if ( !function_exists('secure_html5_video_player_remote_media_exists') ):
function secure_html5_video_player_remote_media_exists($media_server_address, $filename) {
	$has_media_server = secure_html5_video_player_has_media_server();
	if (!$has_media_server) return FALSE;

	$filename_no_ext = secure_html5_video_player_filename_no_ext($filename);
	$filename_normalized_ext = secure_html5_video_player_filename_normalized_ext($filename);
	$access_key = secure_html5_video_player_accessKey($filename);

	$transient_key = 'sh5vp:' . $media_server_address . ':' . $filename_normalized_ext;
	$exists = secure_html5_video_player_get_transient($transient_key);
	if ($exists !== FALSE) {
		return $exists;
	}

	$ext_ary = array('mp4' => '', 'ogv' => '', 'webm' => '', 'png' => '', 'jpg' => '', 'gif' => '');
	
	$media_exists = trim(file_get_contents($media_server_address . '/getinfo.php?k=' . $access_key . '&info=exists&file=' . urlencode($filename_no_ext)));

	$exists = FALSE;
	if ('1' == $media_exists || '0' == $media_exists || '' == $media_exists ) {
	}
	else {
		$lines = explode("\n", $media_exists);
		foreach ($lines as $curr_line) {
			$eq_index = strpos($curr_line, '=');
			if ($eq_index === FALSE) {
				continue;
			}
			$curr_key = substr($curr_line, 0, $eq_index);
			$curr_val = substr($curr_line, $eq_index + 1);
			$ext_ary[$curr_key] = $curr_val;
		}
		foreach ($ext_ary as $ext => $link) {
			if ($link == '') {
				continue;
			}
			if (secure_html5_video_player_endsWith($filename_normalized_ext, '.' . $ext)) {
				$exists = $link;
			}
			$transient_key = 'sh5vp:' . $media_server_address . ':' . $filename_no_ext . '.' . $ext;
			secure_html5_video_player_set_transient($transient_key, $link);
		}
	}
	return $exists;
}
add_filter('secure_html5_video_player_remote_media_exists',
	'secure_html5_video_player_remote_media_exists', 1, 2);
endif;



if ( !function_exists('secure_html5_video_player_youtube_exists') ):
function secure_html5_video_player_youtube_exists($youtube_video_id) {
	if (! $youtube_video_id) {
		return FALSE;
	}
	return TRUE;
}
endif;



if ( !function_exists('secure_html5_video_player_vimeo_exists') ):
function secure_html5_video_player_vimeo_exists($vimeo_video_id) {
	if (! $vimeo_video_id) {
		return FALSE;
	}
	$secure_html5_video_player_youtube_override_type = get_option('secure_html5_video_player_youtube_override_type');
	if ('never' == $secure_html5_video_player_youtube_override_type) {
		return FALSE;
	}
	
	$transient_key = 'sh5vp:vimeo:' . $vimeo_video_id;
	$exists = secure_html5_video_player_get_transient($transient_key);
	if ($exists !== FALSE) {
		return $exists == 'yes';
	}
	
	$headers = get_headers("http://vimeo.com/api/v2/video/{$vimeo_video_id}.php");
	if (strpos($headers[0], '200') > 0) {
		$exists = 'yes';
		secure_html5_video_player_set_transient($transient_key, $exists);
		return TRUE;	
	}
	$exists = 'no';
	secure_html5_video_player_set_transient($transient_key, $exists);
	return FALSE;
}
endif;



if ( !function_exists('secure_html5_video_player_media_server_address_list') ):
function secure_html5_video_player_media_server_address_list() {
	$transient_key = 'sh5vp:media_server_address_list';
	$exists = secure_html5_video_player_get_transient($transient_key);
	if ($exists !== FALSE) {
		return $exists;
	}

	$retval = array();
	$secure_html5_video_player_media_servers = get_option('secure_html5_video_player_media_servers');
	$server_list = explode("\n", $secure_html5_video_player_media_servers);
	foreach ($server_list as $curr_server) {
		$curr_server_val = trim($curr_server);
		if (! $curr_server_val) continue;
		$retval[] = $curr_server_val;
	}
	secure_html5_video_player_set_transient($transient_key, $retval);
	return $retval;
}
endif;



if ( !function_exists('secure_html5_video_player_sub_file_list') ):
function secure_html5_video_player_sub_file_list($secure_html5_video_player_video_dir, $dirname) {
	$video_files = array();
	$curr_path = $secure_html5_video_player_video_dir . '/' . $dirname;
	if (! is_dir($curr_path)) {
		return;
	}
	$dh = opendir($curr_path);
	if ($dh === FALSE) return;
	while (FALSE !== ($filename = readdir($dh))) {
		if (secure_html5_video_player_startsWith($filename, '.')) continue;
		$curr_sub_path = $secure_html5_video_player_video_dir . '/' . $dirname . '/' . $filename;
		if (is_dir($curr_sub_path)) {
			$video_files = array_merge($video_files, secure_html5_video_player_sub_file_list($secure_html5_video_player_video_dir, $dirname . '/' . $filename));
			continue;
		}
		$video_files[ $dirname . '/' . secure_html5_video_player_filename_no_ext($filename) ] = array();				
	}
	return $video_files;
}
endif;



if ( !function_exists('secure_html5_video_player_filelist') ):
function secure_html5_video_player_filelist($does_include_media_server_files) {
	$transient_key = 'sh5vp:filelist_0';
	if ($does_include_media_server_files) {
		$transient_key = 'sh5vp:filelist_1';
	}
	$video_files = secure_html5_video_player_get_transient($transient_key);
	if ($video_files !== FALSE) {
		return $video_files;
	}
	
	$video_files = array();
	$secure_html5_video_player_video_dir = get_option('secure_html5_video_player_video_dir');
		
	if (is_dir($secure_html5_video_player_video_dir)) {
		$dh = opendir($secure_html5_video_player_video_dir);
		while (FALSE !== ($filename = readdir($dh))) {
			if (secure_html5_video_player_startsWith($filename, '.')) continue;
			
			$curr_path = $secure_html5_video_player_video_dir . '/' . $filename;
			if (is_dir($curr_path)) {
				$video_files = array_merge($video_files, secure_html5_video_player_sub_file_list($secure_html5_video_player_video_dir, $filename));
				continue;
			}
			$video_files[ secure_html5_video_player_filename_no_ext($filename) ] = array();
		}
	}

	if (secure_html5_video_player_is_s3_enabled()) {
		$s3_file_list = secure_html5_video_player_s3_file_list();
		foreach ($s3_file_list as $curr_file => $curr_val) {
			$curr_file_val = trim($curr_file);
			if (! $curr_file_val) continue;
			if (isset($video_files[$curr_file_val])) {
				array_push($video_files[$curr_file_val], 's3');
			}
			else {
				$video_files[$curr_file_val] = array('s3');
			}
		}
	}
	
	$has_media_server = secure_html5_video_player_has_media_server();
	if ($does_include_media_server_files && $has_media_server) {
		$server_list = secure_html5_video_player_media_server_address_list();
		foreach ($server_list as $media_server_address) {
			$access_key = secure_html5_video_player_accessKey('');
			$server_files = file_get_contents($media_server_address . '/getinfo.php?k=' . $access_key . '&info=list');
	
			$server_file_list = explode("\n", $server_files);
			foreach ($server_file_list as $curr_file) {
				$curr_file_val = trim($curr_file);
				if (! $curr_file_val) continue;
				if (isset($video_files[$curr_file_val])) {
					array_push($video_files[$curr_file_val], $media_server_address);
				}
				else {
					$video_files[$curr_file_val] = array($media_server_address);
				}
			}
		}
	}
	ksort($video_files);

	secure_html5_video_player_set_transient($transient_key, $video_files);
	return $video_files;
}
endif;



if ( !function_exists('secure_html5_video_player_get_client_ip') ):
function secure_html5_video_player_get_client_ip() {
	if ( isset($_SERVER["REMOTE_ADDR"]) ) {
		return $_SERVER["REMOTE_ADDR"];
	}
	else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) { 
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	else if ( isset($_SERVER["HTTP_CLIENT_IP"]) ) {
		return $_SERVER["HTTP_CLIENT_IP"];
	}
	return FALSE;
}
endif;



if ( !function_exists('secure_html5_video_player_options') ):
function secure_html5_video_player_options() {
	print '<div class="wrap"><h2>';
	_e('Secure HTML5 Video Player', 'secure-html5-video-player');
	print '</h2>';
	if (!empty($_POST)) {
		if (isset($_REQUEST['submit'])) {
			update_secure_html5_video_player_options();
		}
		if (isset($_REQUEST['uninstall'])) {
			secure_html5_video_player_uninstall();
		}
	}
	print '<p>';
	_e('Contributors', 'secure-html5-video-player');
	print ': <a href="http://www.trillamar.com">Lucinda Brown</a>, Jinsoo Kang<br/>';
	_e('Plugin page', 'secure-html5-video-player');
	print ': <a href="http://www.trillamar.com/webcraft/secure-html5-video-player/">www.trillamar.com/webcraft/secure-html5-video-player</a><br/><br/>';
	_e('Secure HTML5 Video Player is a video plugin for WordPress built on the VideoJS HTML5 video player library. It allows you to embed video in your post or page using HTML5 with Flash fallback support for non-HTML5 browsers.  The settings can be easily configured with this control panel and with simplified short codes inserted into posts or pages.  Video files can be served privately; pseudo-streamed from a secured directory.', 'secure-html5-video-player'); 
	print '<br/><br/>';
	printf(
		__('See %s for additional information about Secure HTML5 Video Player.', 'secure-html5-video-player'), 
		'<a href="http://www.trillamar.com/webcraft/secure-html5-video-player/" target="_blank">www.trillamar.com/webcraft/secure-html5-video-player</a>'
	);
	print '<br/>';
	printf(
		__('See %s for additional information about VideoJS.', 'secure-html5-video-player'), 
		'<a href="http://videojs.com/" target="_blank">videojs.com</a>'
	);
	print '<br/></p><br/>';	

	print '<div class="sh5vp_donate_box"><h3>';
	_e('Donate', 'secure-html5-video-player');
	print '</h3>';
	print '<p>';
	_e('If you like this plugin and find it useful, help keep this plugin free and actively developed by making a donation.', 'secure-html5-video-player');
	print '</p>';
	print '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">';
	print '<input type="hidden" name="cmd" value="_donations">';
	print '<input type="hidden" name="business" value="webcraft@trillamar.com">';
	print '<input type="hidden" name="lc" value="US">';
	print '<input type="hidden" name="item_name" value="Trillamar Webcraft - donation to support Secure HTML5 Video Plugin">';
	print '<input type="hidden" name="no_note" value="0">';
	print '<input type="hidden" name="currency_code" value="USD">';
	print '<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">';
	print '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
	print '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';
	print '</form></div>';

	print '<br/></p><br/>';
	print '<form method="post" class="sh5vp_form"><input type="submit" name="submit" class="button-primary" value="';
		_e('Save the options', 'secure-html5-video-player'); 
	print '" />';
	secure_html5_video_player_options_form();
	print '<input type="submit" name="submit" class="button-primary" value="';
		_e('Save the options', 'secure-html5-video-player');
	print '" /><br/><br/><div class="clear"></div></form></div>';
}
endif;



if ( !function_exists('secure_html5_video_player_options_help') ):
function secure_html5_video_player_options_help() {
	print '<label class="title">';
		_e('Video Shortcode Options', 'secure-html5-video-player');
	print '</label>';
	print '<p>';
	print '<h3>file</h3>';
	printf(
		__('The file name of the video without the file extension.  The video directory set in the control panel is searched for files with this name and with file extensions: mp4, m4v, ogv, ogg, theora.ogv, webm, png, jpg, jpeg, and gif.  The files that match are automatically used in the video tag and poster displayed in the page.  For example, if you have videos: %1$s, %2$s, %3$s, and the poster image: %4$s; you need only set a file value of %5$s. To select a video in a subdirectory within the video directory, use the relative path to the video file from the video directory.', 'secure-html5-video-player'), 
		'<b>myclip.mp4</b>', 
		'<b>myclip.ogv</b>', 
		'<b>myclip.webm</b>', 
		'<b>myclip.png</b>', 
		'"myclip"'
	);
	print '<br/><br/><code>[video file="myclip"]</code>';
	print '<br/><br/><code>[video file="path/to/myclip"]</code>';
	
	print '<h3>vimeo</h3>';
	_e('The Vimeo video ID.  A Vimeo video can be used as the primary video, with the HTML5 video as a fallback mechanism if the video is not available on the Vimeo service.  A Vimeo video can alternatively be used as the fallback when a specifed HTML5 video is not available.', 'secure-html5-video-player');
	print '<br/><br/><code>[video vimeo="46623590"]</code>';
	
	print '<h3>youtube</h3>';
	_e('The Youtube video ID.  A Youtube video can be used as the primary video, with the HTML5 video as a fallback mechanism if the video is not available on the Youtube service.  A Youtube video can alternatively be used as the fallback when a specifed HTML5 video is not available.', 'secure-html5-video-player');
	print '<br/><br/><code>[video youtube="u1zgFlCw8Aw"]</code>';
	
	print '<h3>mp4</h3>';
	_e('The file name or URL of the h.264/MP4 source for the video.', 'secure-html5-video-player');
	print '<br/><br/><code>[video mp4="video_clip.mp4"]</code>';
	
	print '<h3>ogg</h3>';
	_e('The file name or URL of the Ogg/Theora source for the video.', 'secure-html5-video-player');
	print '<br/><br/><code>[video ogg="video_clip.ogv"]</code>';
	
	print '<h3>webm</h3>';
	_e('The file name or URL of the VP8/WebM source for the video.', 'secure-html5-video-player');
	print '<br/><br/><code>[video webm="video_clip.webm"]</code>';
	
	print '<h3>poster</h3>';
	_e('The file name or URL of the poster frame for the video.', 'secure-html5-video-player');
	print '<br/><br/><code>[video poster="video_clip.png"]</code>';

	print '<h3>width</h3>';
	_e('The width of the video.', 'secure-html5-video-player');
	print '<br/><br/><code>[video width="640"]</code>';

	print '<h3>height</h3>';
	_e('The height of the video.', 'secure-html5-video-player');
	print '<br/><br/><code>[video height="480"]</code>';

	print '<h3>preload</h3>';
	_e('Start loading the video as soon as possible, before the user clicks play.', 'secure-html5-video-player');
	print '<br/><br/><code>[video preload="yes"]</code>';

	print '<h3>autoplay</h3>';
	_e('Start playing the video as soon as it is ready.', 'secure-html5-video-player');
	print '<br/><br/><code>[video autoplay="yes"]</code>';

	print '<h3>loop</h3>';
	_e('Replay the video from the beginning after it completes playing.', 'secure-html5-video-player');
	print '<br/><br/><code>[video loop="yes"]</code>';

	print '<h3>controls</h3>';
	_e('Enable or disable video playback controls. (Only applies to the "native" skin.)', 'secure-html5-video-player');
	print '<br/><br/><code>[video controls="no"]</code>';

	
	print '<br/><br/><hr/><br/><label class="title">';
		_e('Examples', 'secure-html5-video-player');
	print '</label><br /><br />';
	
	print '<h3>';
		_e('Video URL example', 'secure-html5-video-player');
	print '</h3><code>[video mp4="http://video-js.zencoder.com/oceans-clip.mp4" ogg="http://video-js.zencoder.com/oceans-clip.ogg" webm="http://video-js.zencoder.com/oceans-clip.webm" poster="http://video-js.zencoder.com/oceans-clip.png" preload="yes" autoplay="no" loop="no" width="640" height="264"]</code><br/><h3>';
		_e('Video File Example using default settings', 'secure-html5-video-player');
	print '</h3><code>[video file="video_clip"]</code><br/><h3>';
		_e('Video File Example using custom settings', 'secure-html5-video-player');
	print '</h3><code>[video file="video_clip" preload="yes" autoplay="yes" loop="yes" width="1600" height="900"]</code></p><br/><br/>';
}
endif;



if ( !function_exists('secure_html5_video_player_install') ):
function secure_html5_video_player_install() {
	add_option('secure_html5_video_player_video_dir', ABSPATH . 'videos');
	add_option('secure_html5_video_player_skin', 'tube');
	add_option('secure_html5_video_player_key_seed', base64_encode(AUTH_KEY));
	add_option('secure_html5_video_player_enable_download_fallback', 'yes');
	add_option('secure_html5_video_player_video_shortcode', 'video');
	
	add_option('secure_html5_video_player_default_width', 640);
	add_option('secure_html5_video_player_default_height', 480);
	add_option('secure_html5_video_player_default_preload', 'yes');
	add_option('secure_html5_video_player_default_autoplay', 'no');
	add_option('secure_html5_video_player_default_loop', 'no');
	add_option('secure_html5_video_player_default_controls', 'yes');

	add_option('secure_html5_video_player_enable_media_server', 'no');
	add_option('secure_html5_video_player_media_servers', '');
	add_option('secure_html5_video_player_youtube_override_type', 'fallback');
	add_option('secure_html5_video_player_serve_method', 'file');

	add_option('secure_html5_video_player_enable_s3', 'no');
	add_option('secure_html5_video_player_s3_access_key', '');
	add_option('secure_html5_video_player_s3_secret_key', '');
	add_option('secure_html5_video_player_s3_server', 's3.amazonaws.com');
	add_option('secure_html5_video_player_s3_bucket', '');
	add_option('secure_html5_video_player_s3_video_dir', 'videos/');
	
	add_option('secure_html5_video_player_s3_link_expire', '8');
	add_option('secure_html5_video_player_s3_link_expire_units', 'hours');
	
	add_action('widgets_init', 'secure_html5_video_player_widgets_init' );
}
endif;



if ( !function_exists('secure_html5_video_player_uninstall') ):
function secure_html5_video_player_uninstall() {
	secure_html5_video_player_clear_transient();
	delete_option('secure_html5_video_player_video_dir');
	delete_option('secure_html5_video_player_skin');
	delete_option('secure_html5_video_player_key_seed');
	delete_option('secure_html5_video_player_enable_flash_fallback');
	delete_option('secure_html5_video_player_enable_download_fallback');
	delete_option('secure_html5_video_player_video_shortcode');
	
	delete_option('secure_html5_video_player_default_width');
	delete_option('secure_html5_video_player_default_height');
	delete_option('secure_html5_video_player_default_preload');
	delete_option('secure_html5_video_player_default_autoplay');
	delete_option('secure_html5_video_player_default_loop');
	delete_option('secure_html5_video_player_default_controls');

	delete_option('secure_html5_video_player_enable_media_server');
	delete_option('secure_html5_video_player_media_servers');
	delete_option('secure_html5_video_player_youtube_override_type');
	delete_option('secure_html5_video_player_serve_method');

	delete_option('secure_html5_video_player_enable_s3');
	delete_option('secure_html5_video_player_s3_access_key');
	delete_option('secure_html5_video_player_s3_secret_key');
	delete_option('secure_html5_video_player_s3_server');
	delete_option('secure_html5_video_player_s3_bucket');
	delete_option('secure_html5_video_player_s3_video_dir');

	delete_option('secure_html5_video_player_s3_link_expire');
	delete_option('secure_html5_video_player_s3_link_expire_units');
}
endif;



if ( !function_exists('update_secure_html5_video_player_options') ):
function update_secure_html5_video_player_options() {
	secure_html5_video_player_clear_transient();
	if (isset($_REQUEST['secure_html5_video_player_video_dir'])) {
		update_option('secure_html5_video_player_video_dir', $_REQUEST['secure_html5_video_player_video_dir']);
	}
	if (isset($_REQUEST['secure_html5_video_player_skin'])) {
		update_option('secure_html5_video_player_skin', $_REQUEST['secure_html5_video_player_skin']);
	}
	if (isset($_REQUEST['secure_html5_video_player_key_seed'])) {
		update_option('secure_html5_video_player_key_seed', $_REQUEST['secure_html5_video_player_key_seed']);
	}
	
	if (isset($_REQUEST['secure_html5_video_player_enable_download_fallback']) 
	&& $_REQUEST['secure_html5_video_player_enable_download_fallback'] != '') {
		update_option('secure_html5_video_player_enable_download_fallback', $_REQUEST['secure_html5_video_player_enable_download_fallback']);
	}
	else {
		update_option('secure_html5_video_player_enable_download_fallback', 'no');
	}

	if (isset($_REQUEST['secure_html5_video_player_video_shortcode'])) {
		$new_video_shortcode = trim($_REQUEST['secure_html5_video_player_video_shortcode']);
		if ($new_video_shortcode == '') {
			$new_video_shortcode = 'video';
		}
		update_option('secure_html5_video_player_video_shortcode', $new_video_shortcode);
	}
	
	if (isset($_REQUEST['secure_html5_video_player_default_width'])) {
		update_option('secure_html5_video_player_default_width', $_REQUEST['secure_html5_video_player_default_width']);
	}
	if (isset($_REQUEST['secure_html5_video_player_default_height'])) {
		update_option('secure_html5_video_player_default_height', $_REQUEST['secure_html5_video_player_default_height']);
	}

	if (isset($_REQUEST['secure_html5_video_player_default_preload'])
	&& $_REQUEST['secure_html5_video_player_default_preload'] == 'yes') {
		update_option('secure_html5_video_player_default_preload', 'yes');
	}
	else {
		update_option('secure_html5_video_player_default_preload', 'no');
	}

	if (isset($_REQUEST['secure_html5_video_player_default_autoplay'])
	&& $_REQUEST['secure_html5_video_player_default_autoplay'] == 'yes') {
		update_option('secure_html5_video_player_default_autoplay', 'yes');
	}
	else {
		update_option('secure_html5_video_player_default_autoplay', 'no');
	}

	if (isset($_REQUEST['secure_html5_video_player_default_loop'])
	&& $_REQUEST['secure_html5_video_player_default_loop'] == 'yes') {
		update_option('secure_html5_video_player_default_loop', 'yes');
	}
	else {
		update_option('secure_html5_video_player_default_loop', 'no');
	}

	if (isset($_REQUEST['secure_html5_video_player_default_controls'])
	&& $_REQUEST['secure_html5_video_player_default_controls'] == 'yes') {
		update_option('secure_html5_video_player_default_controls', 'yes');
	}
	else {
		update_option('secure_html5_video_player_default_controls', 'no');
	}
	
	if (isset($_REQUEST['secure_html5_video_player_enable_media_server']) 
	&& $_REQUEST['secure_html5_video_player_enable_media_server'] == 'yes') {
		update_option('secure_html5_video_player_enable_media_server', 'yes');
	}
	else {
		update_option('secure_html5_video_player_enable_media_server', 'no');
	}
	if (isset($_REQUEST['secure_html5_video_player_media_servers'])) {
		update_option('secure_html5_video_player_media_servers', $_REQUEST['secure_html5_video_player_media_servers']);
	}
	
	if (isset($_REQUEST['secure_html5_video_player_youtube_override_type'])) {
		update_option('secure_html5_video_player_youtube_override_type', $_REQUEST['secure_html5_video_player_youtube_override_type']);
	}

	if (isset($_REQUEST['secure_html5_video_player_serve_method'])) {
		update_option('secure_html5_video_player_serve_method', $_REQUEST['secure_html5_video_player_serve_method']);
	}
	
	if (isset($_REQUEST['secure_html5_video_player_enable_s3']) 
	&& $_REQUEST['secure_html5_video_player_enable_s3'] == 'yes') {
		update_option('secure_html5_video_player_enable_s3', 'yes');
	}
	else {
		update_option('secure_html5_video_player_enable_s3', 'no');
	}
	if (isset($_REQUEST['secure_html5_video_player_s3_access_key'])) {
		update_option('secure_html5_video_player_s3_access_key', trim($_REQUEST['secure_html5_video_player_s3_access_key']));
	}
	if (isset($_REQUEST['secure_html5_video_player_s3_secret_key'])) {
		update_option('secure_html5_video_player_s3_secret_key', trim($_REQUEST['secure_html5_video_player_s3_secret_key']));
	}
	if (isset($_REQUEST['secure_html5_video_player_s3_server'])) {
		$sel_s3_server = $_REQUEST['secure_html5_video_player_s3_server'];
		if ($sel_s3_server == 'other') {
			update_option('secure_html5_video_player_s3_server', $_REQUEST['secure_html5_video_player_s3_server_other']);
		}
		else {
			update_option('secure_html5_video_player_s3_server', $sel_s3_server);
		}
	}
	if (isset($_REQUEST['secure_html5_video_player_s3_bucket'])) {
		update_option('secure_html5_video_player_s3_bucket', trim($_REQUEST['secure_html5_video_player_s3_bucket']));
	}
	if (isset($_REQUEST['secure_html5_video_player_s3_video_dir'])) {
		update_option('secure_html5_video_player_s3_video_dir', trim($_REQUEST['secure_html5_video_player_s3_video_dir']));
	}

	if (isset($_REQUEST['secure_html5_video_player_s3_link_expire'])) {
		update_option('secure_html5_video_player_s3_link_expire', $_REQUEST['secure_html5_video_player_s3_link_expire']);
	}
	if (isset($_REQUEST['secure_html5_video_player_s3_link_expire_units'])) {
		update_option('secure_html5_video_player_s3_link_expire_units', $_REQUEST['secure_html5_video_player_s3_link_expire_units']);
	}

}
endif;



if ( !function_exists('secure_html5_video_player_options_form_security') ):
function secure_html5_video_player_options_form_security() {
	$secure_html5_video_player_video_dir = get_option('secure_html5_video_player_video_dir');
	$secure_html5_video_player_key_seed = get_option('secure_html5_video_player_key_seed');
	?>
	<label class="title" for='secure_html5_video_player_video_dir'><?php _e('Video directory', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_video_dir" name='secure_html5_video_player_video_dir' size='100' value='<?php print $secure_html5_video_player_video_dir ?>' /><br/>
	<small>
	<?php 
	printf(
		__('The directory on the website where videos are stored.  Your public_html directory is: %1$s. If videos should be protected, the video directory should either be a password protected directory under public_html like: %2$s; or a location outside of public_html.  This is also where you will upload all of your videos, so it should be a location to where you can FTP large video files.  Your hosting control panel should have more information about creating directories protected from direct web access, and have the necessary functionality to configure them.', 'secure-html5-video-player'), 
		'<code>' . $_SERVER['DOCUMENT_ROOT'] . '</code>',
		'<code>' . $_SERVER['DOCUMENT_ROOT'] . '/videos</code>'
	);
	?>
	</small><br/><br/>

	<label class="title" for='secure_html5_video_player_key_seed'><?php _e('Secure seed', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_key_seed" name='secure_html5_video_player_key_seed'  size='100' value='<?php print $secure_html5_video_player_key_seed ?>' maxlength="80" />
	<input type="button" class="button-secondary" name="buttonGenerateSeed" 
		value="<?php _e('Generate Seed', 'secure-html5-video-player'); ?>" 
		onclick="return sh5vp_generateSeed();" />
	<br/>
	<small><?php 
		printf(
			__('Arbitrary text used to generate session keys for secure video downloads.  This can be any string of any length, up to 80 characters long.  Press the [%s] button to automatically create a random one. If you are using media server(s), this value should be the same across all of them.', 'secure-html5-video-player'),
			__('Generate Seed', 'secure-html5-video-player')			
		); 
		?></small>
	<?php
}
endif;



if ( !function_exists('secure_html5_video_player_options_form_youtube') ):
function secure_html5_video_player_options_form_youtube() {
	$secure_html5_video_player_youtube_override_type = get_option('secure_html5_video_player_youtube_override_type');
	if ($secure_html5_video_player_youtube_override_type == '') {
		$secure_html5_video_player_youtube_override_type = 'fallback';
	}
	$secure_html5_video_player_youtube_override_type_never = '';
	$secure_html5_video_player_youtube_override_type_fallback = '';
	$secure_html5_video_player_youtube_override_type_primary = '';
	switch ($secure_html5_video_player_youtube_override_type) {
		case 'never':
			$secure_html5_video_player_youtube_override_type_never = 'checked="checked"';
			break;
		case 'fallback':
			$secure_html5_video_player_youtube_override_type_fallback = 'checked="checked"';
			break;
		case 'primary':
			$secure_html5_video_player_youtube_override_type_primary = 'checked="checked"';
			break;
	}	
	?>
	<label class="title" for='secure_html5_video_player_youtube_override_type'><?php _e('Allow Youtube or Vimeo to be displayed', 'secure-html5-video-player'); ?>:</label><br /><br />
	<input type="radio" 
		name="secure_html5_video_player_youtube_override_type" 
		id="secure_html5_video_player_youtube_override_type_never"
		value="never"
		<?php print $secure_html5_video_player_youtube_override_type_never ?>
	 /><label for="secure_html5_video_player_youtube_override_type_never"> <?php _e('Never', 'secure-html5-video-player'); ?></label><br /><br />
	<input type="radio" 
		name="secure_html5_video_player_youtube_override_type" 
		id="secure_html5_video_player_youtube_override_type_fallback"
		value="fallback"
		<?php print $secure_html5_video_player_youtube_override_type_fallback ?>
	 /><label for="secure_html5_video_player_youtube_override_type_fallback"> <?php _e('As a fallback, when HTML5 video is not present', 'secure-html5-video-player'); ?></label><br /><br />
	<input type="radio" 
		name="secure_html5_video_player_youtube_override_type" 
		id="secure_html5_video_player_youtube_override_type_primary"
		value="primary"
		<?php print $secure_html5_video_player_youtube_override_type_primary ?>
	 /><label for="secure_html5_video_player_youtube_override_type_primary"> <?php _e('As the primary, but use HTML5 video when the Youtube/Vimeo video is not available', 'secure-html5-video-player'); ?></label><br /><br />
	<div class="inline_help"><?php _e('Allows you to define when Youtube or Vimeo is used as a fallback or as the primary video.', 'secure-html5-video-player'); ?></div>
	<?php
}
endif;



if ( !function_exists('secure_html5_video_player_options_compatibility') ):
function secure_html5_video_player_options_compatibility() {
	$secure_html5_video_player_enable_download_fallback = get_option('secure_html5_video_player_enable_download_fallback');
	if ($secure_html5_video_player_enable_download_fallback == '') {
		$secure_html5_video_player_enable_download_fallback = 'no';
	}
	$secure_html5_video_player_enable_download_fallback_yes = '';
	$secure_html5_video_player_enable_download_fallback_no = '';
	$secure_html5_video_player_enable_download_fallback_always = '';
	switch ($secure_html5_video_player_enable_download_fallback) {
		case 'yes':
			$secure_html5_video_player_enable_download_fallback_yes = 'checked="checked"';
			break;
		case 'no':
			$secure_html5_video_player_enable_download_fallback_no = 'checked="checked"';
			break;
		case 'always':
			$secure_html5_video_player_enable_download_fallback_always = 'checked="checked"';
			break;
	}	
	?>
	<label class="title" for='secure_html5_video_player_youtube_override_type'><?php _e('Enable Video Download Links', 'secure-html5-video-player'); ?>:</label><br />
	<input type="radio" 
		name="secure_html5_video_player_enable_download_fallback" 
		id="secure_html5_video_player_enable_download_fallback_no"
		value="no"
		<?php print $secure_html5_video_player_enable_download_fallback_no ?>
	 /><label for="secure_html5_video_player_enable_download_fallback_no"> <?php _e('Never', 'secure-html5-video-player'); ?></label><br />
	<input type="radio" 
		name="secure_html5_video_player_enable_download_fallback" 
		id="secure_html5_video_player_enable_download_fallback_yes"
		value="yes"
		<?php print $secure_html5_video_player_enable_download_fallback_yes ?>
	 /><label for="secure_html5_video_player_enable_download_fallback_yes"> <?php _e('As a fallback, when HTML5 video cannot be played', 'secure-html5-video-player'); ?></label><br />
	<input type="radio" 
		name="secure_html5_video_player_enable_download_fallback" 
		id="secure_html5_video_player_enable_download_fallback_always"
		value="always"
		<?php print $secure_html5_video_player_enable_download_fallback_always ?>
	 /><label for="secure_html5_video_player_enable_download_fallback_always"> <?php _e('Always', 'secure-html5-video-player'); ?></label><br />
	<div class="inline_help"><?php _e('Allows you to enable or disable download links when the video cannot be played. Select [always] if download links should appear all the time.', 'secure-html5-video-player'); ?></div><br/><br/>

	<?php
	$secure_html5_video_player_video_shortcode = get_option('secure_html5_video_player_video_shortcode', 'video');
	?>
	<label class="title" for='secure_html5_video_player_video_shortcode'><?php _e('Video Shortcode', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_video_shortcode" name='secure_html5_video_player_video_shortcode'  size='50' value='<?php echo $secure_html5_video_player_video_shortcode ?>' /><br/>
	<small><?php _e('Allows you to define a custom shortcode name in the event that a different plugin or template has a conflict with using the [video] shortcode. If you set this to something other than the default, make sure to change all uses of the video shortcode in posts and pages.', 'secure-html5-video-player'); ?></small><br/><br/>
	<?php
}
endif;



if ( !function_exists('secure_html5_video_player_options_form_caching') ):
function secure_html5_video_player_options_form_caching() {
	$secure_html5_video_player_serve_method = get_option('secure_html5_video_player_serve_method');
	if ($secure_html5_video_player_serve_method == '') {
		$secure_html5_video_player_serve_method = 'file';
	}
	$secure_html5_video_player_serve_from_file = '';
	$secure_html5_video_player_serve_from_file_link = '';
	$secure_html5_video_player_serve_dynamically = '';
	switch ($secure_html5_video_player_serve_method) {
		case 'file':
			$secure_html5_video_player_serve_from_file = 'checked="checked"';
			break;
		case 'link':
			$secure_html5_video_player_serve_from_file_link = 'checked="checked"';
			break;
		case 'dynamic':
			$secure_html5_video_player_serve_dynamically = 'checked="checked"';
			break;
	}	
	?>
	<label class="title" for='secure_html5_video_player_serve_method'><?php _e('Video File Serving Methodology', 'secure-html5-video-player'); ?>:</label><br /><br />
	<input type="radio" 
		name="secure_html5_video_player_serve_method" 
		id="secure_html5_video_player_serve_from_file"
		value="file"
		<?php print $secure_html5_video_player_serve_from_file ?>
	 /><label for="secure_html5_video_player_serve_from_file"> <?php _e('Serve from cached files', 'secure-html5-video-player'); ?></label><br /><br />
	<input type="radio" 
		name="secure_html5_video_player_serve_method" 
		id="secure_html5_video_player_serve_from_file_link"
		value="link"
		<?php print $secure_html5_video_player_serve_from_file_link ?>
	 /><label for="secure_html5_video_player_serve_from_file_link"> <?php _e('Serve from cached files using symbolic links', 'secure-html5-video-player'); ?></label><br /><br />
	<input type="radio" 
		name="secure_html5_video_player_serve_method" 
		id="secure_html5_video_player_serve_dynamically"
		value="dynamic"
		<?php print $secure_html5_video_player_serve_dynamically ?>
	 /><label for="secure_html5_video_player_serve_dynamically"> <?php _e('Serve dynamically', 'secure-html5-video-player'); ?></label><br /><br />
	<div class="inline_help"><?php _e('If [serve from cached files] is selected, the video files are hard-linked (or copied) as needed to a temporary cache directory, and served directly to the client using the webserver.  Otherwise, the original video files are loaded and served indirectly using PHP.', 'secure-html5-video-player'); ?></div><br/>
	<div class="inline_help"><?php _e('For hosting providers that place limits on the resources available to PHP, serving dynamically should not chosen because it would not scale well. Choose [serve from cached files] so that the act of serving the video files is handled by the webserver rather than by PHP.', 'secure-html5-video-player'); ?></div><br/>
	<div class="inline_help"><?php _e('Serving from cached files using symbolic links provides the same benefits as the other caching methodology. However, if your webserver is not configured to serve symbolically linked files, the videos will not be able to load properly. Use symbolic linked caching if the files cannot be hard-linked for whatever reason (ie. they are located in different filesystem from the website files).', 'secure-html5-video-player'); ?></div>
	<?php
}
endif;



if ( !function_exists('secure_html5_video_player_options_form_media_server') ):
function secure_html5_video_player_options_form_media_server() {
	$secure_html5_video_player_enable_media_server = ('yes' == get_option('secure_html5_video_player_enable_media_server') ? 'checked="checked"' : '');
	$secure_html5_video_player_media_servers = get_option('secure_html5_video_player_media_servers');
	?>
	<input type='checkbox' value="yes" id="secure_html5_video_player_enable_media_server" name='secure_html5_video_player_enable_media_server' <?php print $secure_html5_video_player_enable_media_server ?> />
	<label class="title" for='secure_html5_video_player_enable_media_server'><?php _e('Enable media servers', 'secure-html5-video-player'); ?></label>
	<br/>
	<small><?php _e('If checked, media is permitted to be loaded from the listed media servers. ', 'secure-html5-video-player'); ?></small>
	<br/><br/>
	<label class="title" for='secure_html5_video_player_media_servers'><?php _e('Media servers', 'secure-html5-video-player'); ?></label><br/>
	<textarea id="secure_html5_video_player_media_servers" name="secure_html5_video_player_media_servers" rows="8" cols="100"><?php print ($secure_html5_video_player_media_servers); ?></textarea><br/>
	<small>
	<?php 
	printf(
		__('A list of media server URLs that serve the media files.  Each URL should be on its own line.  A media server is a separate Wordpress installation with this plugin enabled.  The URL you should list here is the path to the plugin URL on that server.  For example, if this installation is the media server, the URL you should use on the primary webserver is: %1$s.  All media servers must have the same secure seed.', 'secure-html5-video-player'), 
		'<code>' . plugins_url('secure-html5-video-player') . '</code>'
	);
	?>
	</small>
	<?php
}
endif;



if ( !function_exists('secure_html5_video_player_options_form_playback') ):
function secure_html5_video_player_options_form_playback() {
	$secure_html5_video_player_default_width = get_option('secure_html5_video_player_default_width');
	$secure_html5_video_player_default_height = get_option('secure_html5_video_player_default_height');
	$secure_html5_video_player_default_preload = ('yes' == get_option('secure_html5_video_player_default_preload') ? 'checked="checked"' : '');
	$secure_html5_video_player_default_autoplay = ('yes' == get_option('secure_html5_video_player_default_autoplay') ? 'checked="checked"' : '');
	$secure_html5_video_player_default_loop = ('yes' == get_option('secure_html5_video_player_default_loop') ? 'checked="checked"' : '');
	$secure_html5_video_player_default_controls = ('no' != get_option('secure_html5_video_player_default_controls') ? 'checked="checked"' : '');
	?>
	<label class="title" for='secure_html5_video_player_default_width'><?php _e('Default width', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_default_width" name='secure_html5_video_player_default_width'  size='10' value='<?php print $secure_html5_video_player_default_width ?>' /> px<br/>
	<small><?php 
		printf(
			__('Default video width.  Can be overrided by setting the %s attribute in the short tag.', 'secure-html5-video-player'),
			'<b>width</b>'
		); 
	?></small>
	<br/><br/>
	
	<label class="title" for='secure_html5_video_player_default_height'><?php _e('Default height', 'secure-html5-video-player'); ?></label><br/>
	<input type='text' id="secure_html5_video_player_default_height" name='secure_html5_video_player_default_height' size='10' value='<?php print $secure_html5_video_player_default_height ?>' /> px<br/>
	<small><?php 
		printf(
			__('Default video height.  Can be overrided by setting the %s attribute in the short tag.', 'secure-html5-video-player'),
			'<b>height</b>'
		); ?></small>
	<br/><br/>

	<input type='checkbox' value="yes" id="secure_html5_video_player_default_preload" name='secure_html5_video_player_default_preload' <?php print $secure_html5_video_player_default_preload ?> />
	<label class="title" for='secure_html5_video_player_default_preload'><?php _e("Preload video", 'secure-html5-video-player'); ?></label>
	<br/>
	<small><?php 
		printf(
			__('If checked, the video will preload by default.  Can be overrided by setting the %1$s attribute in the short tag to %2$s or %3$s.', 'secure-html5-video-player'),
			'<b>preload</b>',
			'<b>yes</b>',
			'<b>no</b>'
		); ?></small>
	<br/><br/>

	<input type='checkbox' value="yes" id="secure_html5_video_player_default_autoplay" name='secure_html5_video_player_default_autoplay' <?php print $secure_html5_video_player_default_autoplay ?> />
	<label class="title" for='secure_html5_video_player_default_autoplay'><?php _e('Autoplay video', 'secure-html5-video-player'); ?></label>
	<br/>
	<small><?php 
		printf(
			__('If checked, the video start playing automatically when the page is loaded.  Can be overrided by setting the %1$s attribute in the short tag to %2$s or %3$s.', 'secure-html5-video-player'),
			'<b>autoplay</b>',
			'<b>yes</b>',
			'<b>no</b>'
		); ?></small>
	<br/><br/>

	<input type='checkbox' value="yes" id="secure_html5_video_player_default_loop" name='secure_html5_video_player_default_loop' <?php print $secure_html5_video_player_default_loop ?> />
	<label class="title" for='secure_html5_video_player_default_loop'><?php _e('Loop video', 'secure-html5-video-player'); ?></label>
	<br/>
	<small><?php 
		printf(
			__('If checked, the video will play again after it finishes.  Can be overrided by setting the %1$s attribute in the short tag to %2$s or %3$s.', 'secure-html5-video-player'),
			'<b>loop</b>',
			'<b>yes</b>',
			'<b>no</b>'
		); ?></small>
	<br/><br/>

	<input type='checkbox' value="yes" id="secure_html5_video_player_default_controls" name='secure_html5_video_player_default_controls' <?php print $secure_html5_video_player_default_controls ?> />
	<label class="title" for='secure_html5_video_player_default_controls'><?php _e('Video Player Controls', 'secure-html5-video-player'); ?></label>
	<br/>
	<small><?php 
		printf(
			__('If checked, the video player will show playback controls by default.  Can be overrided by setting the %1$s attribute in the short tag to %2$s or %3$s.', 'secure-html5-video-player'),
			'<b>controls</b>',
			'<b>yes</b>',
			'<b>no</b>'
		); ?></small>
	<br/><br/>
	<?php
}
endif;



if ( !function_exists('secure_html5_video_player_options_form_skin') ):
function secure_html5_video_player_options_form_skin() {
	$secure_html5_video_player_skin = get_option('secure_html5_video_player_skin');
	if ($secure_html5_video_player_skin == '') {
		$secure_html5_video_player_skin = 'native';
	}
	$plugin_dir = plugins_url('secure-html5-video-player');
	$skin_ary = array(
		'native', 'tube', 'vim', 'hu', 'videojs'
	);
	?>
	<label class="title" for='secure_html5_video_player_skin'><?php _e('Player Skin', 'secure-html5-video-player'); ?></label>
	<input type="hidden" id="secure_html5_video_player_skin" name='secure_html5_video_player_skin' value="<?php print $secure_html5_video_player_skin; ?>" />
	<div class="sh5vp_skin_select_box">
	<?php foreach ($skin_ary as $curr_skin) { ?>
		<a class="sh5vp_skin_select" id="sh5vp_skin_select-<?php print $curr_skin; ?>" onclick="return sh5vp_selectSkin('<?php print $curr_skin; ?>');"><div><?php print $curr_skin; ?></div><img src="<?php print $plugin_dir ?>/images/<?php print $curr_skin; ?>.png" /></a>
	<?php } ?>
	</div><br/>
	<div class="inline_help clear"><?php _e('The look and feel of the HTML5 video player. Select [native] to use the browser\'s default video player appearance.', 'secure-html5-video-player'); ?></div><br />
	<?php
}
endif;



if ( !function_exists('secure_html5_video_player_options_form') ):
function secure_html5_video_player_options_form() {
	wp_enqueue_style('dashboard');
	wp_print_styles('dashboard');
	wp_enqueue_script('dashboard');
	wp_print_scripts('dashboard');
?>
<script>
jQuery(document).ready(function() {
	sh5vp_initTabs();
	<?php if (isset($_REQUEST['last_tab_sel']) && $_REQUEST['last_tab_sel']) { ?>
		sh5vp_setActiveTab(jQuery('#<?php print $_REQUEST['last_tab_sel']; ?>'));
	<?php } else { ?>
		sh5vp_setActiveTab(jQuery('.sh5vp_tabs .sh5vp_tab').first());
	<?php } ?>
});
</script>
<input type="hidden" id="last_tab_sel" name="last_tab_sel" value="" />
<div class="tab_panel">
<div class="sh5vp_tabs">
<ul>
	<li id="sh5vp_tab_link1" class="sh5vp_tab" href="#" rel="sh5vp_tab_1"><?php 
		_e('Security', 'secure-html5-video-player'); 
	?></li>
	<li id="sh5vp_tab_link2" class="sh5vp_tab" href="#" rel="sh5vp_tab_2"><?php 
		_e('Caching', 'secure-html5-video-player'); 
	?></li>
	<li id="sh5vp_tab_link9" class="sh5vp_tab" href="#" rel="sh5vp_tab_9"><?php 
		_e('S3', 'secure-html5-video-player');
	?></li>
	<li id="sh5vp_tab_link3" class="sh5vp_tab" href="#" rel="sh5vp_tab_3"><?php 
		_e('Media Server', 'secure-html5-video-player'); 
	?></li>
	<li id="sh5vp_tab_link4" class="sh5vp_tab" href="#" rel="sh5vp_tab_4"><?php 
		_e('Youtube/Vimeo', 'secure-html5-video-player'); 
	?></li>
	<li id="sh5vp_tab_link5" class="sh5vp_tab" href="#" rel="sh5vp_tab_5"><?php 
		_e('Playback', 'secure-html5-video-player'); 
	?></li>
	<li id="sh5vp_tab_link6" class="sh5vp_tab" href="#" rel="sh5vp_tab_6"><?php 
		_e('Skin', 'secure-html5-video-player'); 
	?></li>
	<li id="sh5vp_tab_link7" class="sh5vp_tab" href="#" rel="sh5vp_tab_7"><?php 
		_e('Compatibility', 'secure-html5-video-player'); 
	?></li>
	<li id="sh5vp_tab_link8" class="sh5vp_tab" href="#" rel="sh5vp_tab_8"><?php 
		_e('Help', 'secure-html5-video-player'); 
	?></li>
</ul>
</div>
<div class="sh5vp_content">
	<div class="sh5vp_content_tab" id="sh5vp_tab_1">
		<div class="sh5vp-wrapper"><?php 
			secure_html5_video_player_options_form_security(); 
		?></div>
	</div>
	<div class="sh5vp_content_tab" id="sh5vp_tab_2">
		<div class="sh5vp-wrapper"><?php 
			secure_html5_video_player_options_form_caching(); 
		?></div>
	</div>
	<div class="sh5vp_content_tab" id="sh5vp_tab_9">
		<div class="sh5vp-wrapper"><?php 
			secure_html5_video_player_options_form_s3();
		?></div>
	</div>
	<div class="sh5vp_content_tab" id="sh5vp_tab_3">
		<div class="sh5vp-wrapper"><?php 
			secure_html5_video_player_options_form_media_server();
		?></div>
	</div>
	<div class="sh5vp_content_tab" id="sh5vp_tab_4">
		<div class="sh5vp-wrapper"><?php 
			secure_html5_video_player_options_form_youtube();	
		?></div>
	</div>
	<div class="sh5vp_content_tab" id="sh5vp_tab_5">
		<div class="sh5vp-wrapper"><?php
			secure_html5_video_player_options_form_playback();
		?></div>
	</div>
	<div class="sh5vp_content_tab" id="sh5vp_tab_6">
		<div class="sh5vp-wrapper skin"><?php
			secure_html5_video_player_options_form_skin();
		?></div>
	</div>
	<div class="sh5vp_content_tab" id="sh5vp_tab_7">
		<div class="sh5vp-wrapper"><?php
			secure_html5_video_player_options_compatibility();
		?></div>
	</div>
	<div class="sh5vp_content_tab" id="sh5vp_tab_8">
		<div class="sh5vp-wrapper"><?php secure_html5_video_player_options_help(); ?></div>
	</div>
</div>
</div><br />
	<?php
}
endif;



if ( !function_exists('secure_html5_video_player_printFile') ):
function secure_html5_video_player_printFile($file) {
	$fp = fopen($file, "r");
	if (!$fp) return;
	$chars_sent = 0;
	while (!feof($fp)) {
		$content = fread($fp, 1024);
		print $content;
		$chars_sent += strlen($content);
		if (connection_aborted()) {
			break;
		}
	}
	fclose($fp);
}
endif;



if ( !function_exists('secure_html5_video_player_filename_no_ext') ):
function secure_html5_video_player_filename_no_ext($str) {
	$retval = $str;
	$pos = strrpos($str, '.');
	if ($pos > 0) {
		$retval = substr($str, 0, $pos);
	}
	if (secure_html5_video_player_endsWith($retval, '.theora')) {
		$retval = secure_html5_video_player_filename_no_ext($retval);
	}
	return $retval;
}
endif;



if ( !function_exists('secure_html5_video_player_filename_normalized_ext') ):
function secure_html5_video_player_filename_normalized_ext($str) {
	$filename_no_ext = secure_html5_video_player_filename_no_ext($str);
	$ext = secure_html5_video_player_filename_get_ext($str);
	$normalized_ext = secure_html5_video_player_filename_get_normalized_ext($ext);
	return $filename_no_ext . '.' . $normalized_ext;
}
endif;



if ( !function_exists('secure_html5_video_player_filename_get_ext') ):
function secure_html5_video_player_filename_get_ext($str) {
	$filename_no_ext = secure_html5_video_player_filename_no_ext($str);
	$ext = strtolower(substr($str, strlen($filename_no_ext) + 1));
	return $ext;
}
endif;



if ( !function_exists('secure_html5_video_player_filename_get_normalized_ext') ):
function secure_html5_video_player_filename_get_normalized_ext($ext) {
	$normalized_ext = $ext;
	if ($ext == 'm4v') {
		$normalized_ext = 'mp4';
	}
	else if ($ext == 'ogg' || $ext == 'theora.ogv' || $ext == 'theora.ogg' ) {
		$normalized_ext = 'ogv';
	}
	else if ($normalized_ext == 'jpeg') {
		$normalized_ext = 'jpg';
	}
	return $normalized_ext;
}
endif;



if ( !function_exists('secure_html5_video_player_to_object_id') ):
function secure_html5_video_player_to_object_id($prefix, $str) {
	$retval = secure_html5_video_player_filename_no_ext($str);
	$trans = array(
		"?" => "-", 
		"=" => "-", 
		"&" => "-",
		"." => "-",
		"$" => "-",
		"%" => "-"
	);
	return $prefix . strtr($retval, $trans);
}
endif;



if ( !function_exists('secure_html5_video_player_endsWith') ):
function secure_html5_video_player_endsWith($Haystack, $Needle){
	return strrpos($Haystack, $Needle) === strlen($Haystack) - strlen($Needle);
}
endif;



if ( !function_exists('secure_html5_video_player_startsWith') ):
function secure_html5_video_player_startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}
endif;



if ( !function_exists('secure_html5_video_player_accessKey') ):
function secure_html5_video_player_accessKey($filename) {
	$secure_html5_video_player_key_seed = get_option('secure_html5_video_player_key_seed');
	$script_tz = date_default_timezone_get();
	//date_default_timezone_set('America/Los_Angeles');
	date_default_timezone_set(get_option('timezone_string'));
	$f = '';
	if ($filename) {
		$f = secure_html5_video_player_filename_no_ext($filename);
	}
	$access_key = sha1(date('d-n-y') . $secure_html5_video_player_key_seed . $f);
	date_default_timezone_set($script_tz);
	return $access_key;
}
endif;



if ( !function_exists('secure_html5_video_player_add_header') ):
function secure_html5_video_player_add_header() {
	$bd = SH5VP_BrowserDetect::detect();
	if ($bd->isMobileBrowser()) {
		return;
	}
	$secure_html5_video_player_skin = get_option('secure_html5_video_player_skin');
	$plugin_dir = plugins_url('secure-html5-video-player');
	if ($secure_html5_video_player_skin != 'native') {
		print "<link rel='stylesheet' href='{$plugin_dir}/video-js/video-js.css' type='text/css' />\n";
		if ($secure_html5_video_player_skin != 'videojs') {
			print "<link rel='stylesheet' href='{$plugin_dir}/video-js/skins/".$secure_html5_video_player_skin.".css' type='text/css' />\n";
		}
		print "<script src='{$plugin_dir}/video-js/video.js' type='text/javascript' ></script>\n";
		print "<script type='text/javascript' > VideoJS.setupAllWhenReady(); </script>\n";
	}
}
endif;



if ( !function_exists('secure_html5_video_player_shortcode_video') ):
function secure_html5_video_player_shortcode_video($atts) {
	$bd = SH5VP_BrowserDetect::detect();
	
	$video_tag = '';
	$count_file_exists = 0;

	$secure_html5_video_player_enable_download_fallback = get_option('secure_html5_video_player_enable_download_fallback');
	$secure_html5_video_player_youtube_override_type = get_option('secure_html5_video_player_youtube_override_type');
	$secure_html5_video_player_video_dir = get_option('secure_html5_video_player_video_dir');
	$secure_html5_video_player_skin = get_option('secure_html5_video_player_skin');
	$plugin_dir = plugins_url('secure-html5-video-player');
	
  extract(shortcode_atts(array(
    'file' => '',
    'mp4' => '',
    'webm' => '',
    'ogg' => '',
    'poster' => '',
    'width' => get_option('secure_html5_video_player_default_width'),
		'height' => get_option('secure_html5_video_player_default_height'),
    'preload' => get_option('secure_html5_video_player_default_preload'),
    'autoplay' => get_option('secure_html5_video_player_default_autoplay'),
    'loop' => get_option('secure_html5_video_player_default_loop'),
    'controls' => get_option('secure_html5_video_player_default_controls'),
    'youtube' => '',
    'vimeo' => ''
  ), $atts));
	
	if (!$width || $width <= 0) {
		$width = '640';
	}
	if (!$height || $height <= 0) {
		$height = '480';
	}

	$youtube_tag = '';
	$youtube_exists = secure_html5_video_player_youtube_exists($youtube);
	if ($youtube_exists) {
		$autoplay_youtube = '0';
		if ($autoplay == 'yes' || $autoplay == 'true') {
			$autoplay_youtube = '1';
		}
		$origin = urlencode(site_url());
		$object_tag_id = secure_html5_video_player_to_object_id('ytplayer-', $youtube);
		$youtube_tag .= "<!-- Begin - Secure HTML5 Video Player -->\n";
		$youtube_tag .= "<iframe id='{$object_tag_id}' type='text/html' width='{$width}' height='{$height}' src='http://www.youtube.com/embed/{$youtube}?autoplay={$autoplay_youtube}&origin={$origin}' frameborder='0' /></iframe>\n";
		$youtube_tag .= "<!-- End - Secure HTML5 Video Player -->\n";
	}
	
	$vimeo_tag = '';
	$vimeo_exists = secure_html5_video_player_vimeo_exists($vimeo);
	if ($vimeo_exists) {
		$autoplay_vimeo = '0';
		if ($autoplay == 'yes' || $autoplay == 'true') {
			$autoplay_vimeo = '1';
		}
		$loop_vimeo = '0';
		if ($loop == 'yes' || $loop == 'true') {
			$loop_vimeo = '1';
		}
		$object_tag_id = secure_html5_video_player_to_object_id('vimeoplayer-', $vimeo);
		$vimeo_tag .= "<!-- Begin - Secure HTML5 Video Player -->\n";
		$vimeo_tag .= "<iframe id='{$object_tag_id}' src='http://player.vimeo.com/video/{$vimeo}?autoplay={$autoplay_vimeo}&amp;loop={$loop_vimeo}' width='{$width}' height='{$height}' frameborder='0'></iframe>";
		$vimeo_tag .= "<!-- End - Secure HTML5 Video Player -->\n";
	}

	{
		$video_tag .= "<!-- Begin - Secure HTML5 Video Player -->\n";
		if ($file) {
			$file = secure_html5_video_player_filename_no_ext($file);
		}
		$is_s3_enabled = secure_html5_video_player_is_s3_enabled();
		$has_media_server = secure_html5_video_player_has_media_server();
		$object_tag_id = '';
		if ($file) {
			$object_tag_id = secure_html5_video_player_to_object_id('vjs-ff-', $file);
			$access_key = secure_html5_video_player_accessKey($file);
			
			$remote_mp4_link = '';
			$remote_webm_link = '';
			$remote_ogv_link = '';
			$remote_jpg_link = '';
			$remote_png_link = '';
			$remote_gif_link = '';
	
			if ($is_s3_enabled) {
				$remote_mp4_link = apply_filters('secure_html5_video_player_s3_media_exists', "{$file}.mp4");
				$remote_webm_link = apply_filters('secure_html5_video_player_s3_media_exists', "{$file}.webm");
				$remote_ogv_link = apply_filters('secure_html5_video_player_s3_media_exists', "{$file}.ogv");
				$remote_jpg_link = apply_filters('secure_html5_video_player_s3_media_exists', "{$file}.jpg");
				$remote_png_link = apply_filters('secure_html5_video_player_s3_media_exists', "{$file}.png");
				$remote_gif_link = apply_filters('secure_html5_video_player_s3_media_exists', "{$file}.gif");
				
				if ($remote_mp4_link || $remote_webm_link || $remote_ogv_link) {
					$has_media_server = FALSE;
				}
			}
			if ($has_media_server) {
				$media_plugin_dir = apply_filters('secure_html5_video_player_get_media_server_address', secure_html5_video_player_get_client_ip(), $file);
				$video_tag .= "<!-- Using media server: " .$media_plugin_dir. " -->\n";
				if (! $remote_mp4_link) {
					$remote_mp4_link = apply_filters('secure_html5_video_player_remote_media_exists', $media_plugin_dir, "{$file}.mp4");
				}
				if (! $remote_webm_link) {
					$remote_webm_link = apply_filters('secure_html5_video_player_remote_media_exists', $media_plugin_dir, "{$file}.webm");
				}
				if (! $remote_ogv_link) {
					$remote_ogv_link = apply_filters('secure_html5_video_player_remote_media_exists', $media_plugin_dir, "{$file}.ogv");
				}
				if (! $remote_jpg_link) {
					$remote_jpg_link = apply_filters('secure_html5_video_player_remote_media_exists', $media_plugin_dir, "{$file}.jpg");
				}
				if (! $remote_png_link) {
					$remote_png_link = apply_filters('secure_html5_video_player_remote_media_exists', $media_plugin_dir, "{$file}.png");
				}
				if (! $remote_gif_link) {
					$remote_gif_link = apply_filters('secure_html5_video_player_remote_media_exists', $media_plugin_dir, "{$file}.gif");
				}
			}
			
			if ($remote_mp4_link) {
				$mp4 = $remote_mp4_link;
				$count_file_exists++;
			}
			else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.mp4")) {
				$mp4 = secure_html5_video_player_media_url(
					$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'mp4'
				);
				$count_file_exists++;
			}
			else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.m4v")) {
				$mp4 = secure_html5_video_player_media_url(
					$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'm4v'
				);
				$count_file_exists++;
			}
			
			if ($remote_webm_link) {
				$webm = $remote_webm_link;
				$count_file_exists++;
			}
			else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.webm")) {
				$webm = secure_html5_video_player_media_url(
					$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'webm'
				);
				$count_file_exists++;
			}
			
			if ($remote_ogv_link) {
				$ogg = $remote_ogv_link;
				$count_file_exists++;
			}
			else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.ogv")) {
				$ogg = secure_html5_video_player_media_url(
					$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'ogv'
				);
				$count_file_exists++;
			}
			else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.ogg")) {
				$ogg = secure_html5_video_player_media_url(
					$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'ogg'
				);
				$count_file_exists++;
			}
			else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.theora.ogv")) {
				$ogg = secure_html5_video_player_media_url(
					$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'theora.ogv'
				);
				$count_file_exists++;
			}
			
			if (!$poster) {
				if ($remote_png_link) {
					$poster = $remote_png_link;
				}
				else if ($remote_jpg_link) {
					$poster = $remote_jpg_link;
				}
				else if ($remote_gif_link) {
					$poster = $remote_gif_link;
				}
				else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.jpg")) {
					$poster = secure_html5_video_player_media_url(
						$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'jpg'
					);
				}
				else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.jpeg")) {
					$poster = secure_html5_video_player_media_url(
						$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'jpeg'
					);
				}
				else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.png")) {
					$poster = secure_html5_video_player_media_url(
						$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'png'
					);
				}
				else if (file_exists("{$secure_html5_video_player_video_dir}/{$file}.gif")) {
					$poster = secure_html5_video_player_media_url(
						$secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, 'gif'
					);
				}
			}
		}	

		$fallback_plugin_dir = urlencode($plugin_dir);
		
		// MP4 Source Supplied
		if ($mp4) {
			if (! $object_tag_id) {
				$object_tag_id = secure_html5_video_player_to_object_id('vjs-ff-', $mp4);
			}
			$mp4_source = '<source src="'.$mp4.'" type="video/mp4" />';
			$mp4_link = '<a class="sh5vp-link-mp4" href="'.$mp4.'">MP4</a>';
			$fallback_mp4 = urlencode($mp4);
			$count_file_exists++;
		}
	
		// WebM Source Supplied
		if ($webm) {
			$webm_source = '<source src="'.$webm.'" type="video/webm" />';
			$webm_link = '<a class="sh5vp-link-webm" href="'.$webm.'">WebM</a>';
			$count_file_exists++;
		}
	
		// Ogg source supplied
		if ($ogg) {
			$ogg_source = '<source src="'.$ogg.'" type="video/ogg" />';
			$ogg_link = '<a class="sh5vp-link-ogg" href="'.$ogg.'">Ogg</a>';
			$count_file_exists++;
		}
	
		if ($poster) {
			$poster_attribute = 'poster="'.$poster.'"';
		}
	
		if ($preload == 'yes' || $preload == 'true') {
			$preload_attribute = 'preload="auto"';
		}
		else {
			$preload_attribute = 'preload="none"';
		}
	
		if ($autoplay == 'yes' || $autoplay == 'true') {
			$autoplay_attribute = 'autoplay="autoplay"';
			$fallback_autoplay = '1';
		}
		else {
			$autoplay_attribute = "";
			$fallback_autoplay = '0';
		}
	
		if ($loop == 'yes' || $loop == 'true') {
			$loop_attribute = 'loop="loop"';
		}
		else {
			$loop_attribute = "";
		}
	
		if ($controls != 'no' && $controls != 'false') {
			$controls_attribute = 'controls="controls"';
		}
		else {
			$controls_attribute = "";
		}
		
		$video_tag_skin = '';
		if ($secure_html5_video_player_skin != 'videojs') {
			$video_tag_skin = $secure_html5_video_player_skin . '-css';
		}
		$video_tag .= "<div class='video-js-box sh5vp-video-box {$video_tag_skin}' >\n";
		
		if ($bd->isMobileBrowser()) {
			// iOS and Android devices
			$video_tag .= "<video class='video-js sh5vp-video' onclick='this.play();' width='{$width}' height='{$height}' {$poster_attribute} {$controls_attribute} {$preload_attribute} {$autoplay_attribute} {$loop_attribute} >\n";
			if ($mp4_source) {
				$video_tag .= "{$mp4_source}\n";
			}
			$video_tag .= "</video>\n";
		}
		else if ($bd->isIE() && $bd->versionIE() <= 8 && $mp4) {
			// IE 7 or IE 8
			$video_tag .= "<iframe id='{$object_tag_id}' type='text/html' width='{$width}' height='{$height}' src='{$plugin_dir}/fallback/index.php?autoplay={$fallback_autoplay}&mp4={$fallback_mp4}&url={$fallback_plugin_dir}' frameborder='0' scrolling='no' seamless='seamless' /></iframe>\n";
			if ('always' == $secure_html5_video_player_enable_download_fallback) {
				$video_tag .= "<p class='sh5vp-download-links'><label>Download Video:</label>\n";
				if ($mp4_link) {
					$video_tag .= "{$mp4_link}\n";
				}
				if ($webm_link) {
					$video_tag .= "{$webm_link}\n";
				}
				if ($ogg_link) {
					$video_tag .= "{$ogg_link}\n";
				}
				$video_tag .= "</p>\n";
			}
		}
		else {
			// everything else
			if ($count_file_exists == 0) {
				$video_tag .= "<!-- " . __('file not found', 'secure-html5-video-player') . ": {$secure_html5_video_player_video_dir}/{$file} -->\n";
			}
			else if ($bd->isFirefox() && ($bd->versionFirefox() < 21 || $bd->isMac()) && $mp4 && !($ogg || $webm)) {
				$video_tag .= "<iframe id='{$object_tag_id}' type='text/html' width='{$width}' height='{$height}' src='{$plugin_dir}/fallback/index.php?autoplay={$fallback_autoplay}&mp4={$fallback_mp4}&url={$fallback_plugin_dir}' frameborder='0' /></iframe>\n";
			}
			else {
				$video_tag .= "<video class='video-js sh5vp-video' width='{$width}' height='{$height}' {$poster_attribute} {$controls_attribute} {$preload_attribute} {$autoplay_attribute} {$loop_attribute} >\n";
				if ($mp4_source) {
					$video_tag .= "{$mp4_source}\n";
				}
				if ($webm_source) {
					$video_tag .= "{$webm_source}\n";
				}
				if ($ogg_source) {
					$video_tag .= "{$ogg_source}\n";
				}
				$video_tag .= "</video>\n";
			}				
			//Download links provided for devices that can't play video in the browser.
			if ('no' != $secure_html5_video_player_enable_download_fallback) {
				$can_play_provided = secure_html5_video_player_can_play($mp4_link, $ogg_link, $webm_link);
				if (! $can_play_provided || 'always' == $secure_html5_video_player_enable_download_fallback) {
					$video_tag .= "<p class='sh5vp-download-links'><label>Download Video:</label>\n";
					if ($mp4_link) {
						$video_tag .= "{$mp4_link}\n";
					}
					if ($webm_link) {
						$video_tag .= "{$webm_link}\n";
					}
					if ($ogg_link) {
						$video_tag .= "{$ogg_link}\n";
					}
					$video_tag .= "</p>\n";
				}
			}
		}
		$video_tag .= "</div>\n";
		$video_tag .= "<!-- End - Secure HTML5 Video Player -->\n";
	}
	if ($vimeo_exists) {
		if ($count_file_exists == 0) {
			return $vimeo_tag;
		}
		else if ('primary' == $secure_html5_video_player_youtube_override_type) {
			return $vimeo_tag;
		}	
	}
	else if ($youtube_exists) {
		if ($count_file_exists == 0) {
			return $youtube_tag;
		}
		else if ('primary' == $secure_html5_video_player_youtube_override_type) {
			return $youtube_tag;
		}	
	}
	return $video_tag;
}
endif;



if ( !function_exists('secure_html5_video_player_parent_path_with_file') ):
function secure_html5_video_player_parent_path_with_file($filepath, $needle, $limit) {
	$curr_path = dirname($filepath);
	for ($i = 0; $i < $limit; $i++) {
		$ls = scandir($curr_path);
		if (isset($ls) && is_array($ls) && in_array($needle, $ls)) return $curr_path;
		$curr_path = dirname($curr_path);
	}
	return NULL;
}
endif;



if ( !function_exists('secure_html5_video_player_widgets_init') ):
function secure_html5_video_player_widgets_init() {
	register_widget( 'secure_html5_video_player_widget' );
}
endif;



if ( !function_exists('secure_html5_video_player_plugins_loaded') ):
function secure_html5_video_player_plugins_loaded() {
	load_plugin_textdomain(
		'secure-html5-video-player', 
		FALSE, 
		dirname( plugin_basename( __FILE__ ) ) . '/languages/'
	);
	add_action('widgets_init', 'secure_html5_video_player_widgets_init' );
}
endif;



if ( !function_exists('secure_html5_video_player_rcopy') ):
function secure_html5_video_player_rcopy($source, $dest){
	if (is_dir($source)) {
		$dir_handle = opendir($source);
		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (is_dir($source . "/" . $file)) {
					mkdir($dest . "/" . $file);
					secure_html5_video_player_rcopy($source . "/" . $file, $dest . "/" . $file);
				}
				else {
					copy($source."/".$file, $dest."/".$file);
				}
			}
		}
		closedir($dir_handle);
	}
	else {
		copy($source, $dest);
	}
}
endif;



if ( !function_exists('secure_html5_video_player_media_iframe_url') ):
function secure_html5_video_player_media_iframe_url($opts) {
	//$opts - hash with keys: file, youtube, vimeo, width, height, preload, autoplay, loop
	$youtube_override_type = get_option('secure_html5_video_player_youtube_override_type');
	if ('primary' == $youtube_override_type) {
		if (secure_html5_video_player_youtube_exists($opts['youtube'])) {
			$youtube = $opts['youtube'];
			$autoplay_youtube = '0';
			if ($opts['autoplay'] == 'yes' || $opts['autoplay'] == 'true') {
				$autoplay_youtube = '1';
			}
			$origin = urlencode(site_url());
			return "http://www.youtube.com/embed/{$youtube}?autoplay={$autoplay_youtube}&origin={$origin}";
		}
		else if (secure_html5_video_player_vimeo_exists($opts['vimeo'])) {
			$vimeo = $opts['vimeo'];
			$autoplay_vimeo = '0';
			if ($opts['autoplay'] == 'yes' || $opts['autoplay'] == 'true') {
				$autoplay_vimeo = '1';
			}
			$loop_vimeo = '0';
			if ($opts['loop'] == 'yes' || $opts['loop'] == 'true') {
				$loop_vimeo = '1';
			}
			return "http://player.vimeo.com/video/{$vimeo}?autoplay={$autoplay_vimeo}&amp;loop={$loop_vimeo}";
		}
	}
	$access_key = secure_html5_video_player_accessKey($file);
	$plugin_dir = plugins_url('secure-html5-video-player');
	return "{$plugin_dir}/getiframe.php?k=" . urlencode($access_key) 
		. "&file=" . urlencode($opts['file'])
		. "&youtube=" . urlencode($opts['youtube'])
		. "&vimeo=" . urlencode($opts['vimeo'])
		. "&preload=" . urlencode($opts['preload'])
		. "&autoplay=" . urlencode($opts['autoplay'])
		. "&loop=" . urlencode($opts['loop']);
}
endif;



if ( !function_exists('secure_html5_video_player_media_url') ):
function secure_html5_video_player_media_url($secure_html5_video_player_video_dir, $plugin_dir, $access_key, $file, $ext) {
	$dynamic_url = "{$plugin_dir}/getvideo.php?k=" . urlencode($access_key) . "&file=" . urlencode($file . '.' . $ext);
	
	$secure_html5_video_player_serve_method = get_option('secure_html5_video_player_serve_method');
	if ($secure_html5_video_player_serve_method == '') {
		$secure_html5_video_player_serve_method = 'file';
	}
	if ($secure_html5_video_player_serve_method == 'dynamic') {
		file_get_contents("{$plugin_dir}/prepvideo.php?k=" . urlencode($access_key) . "&onlyclean=1&file=" . urlencode($file . '.' . $ext));
		return $dynamic_url;
	}

	$script_tz = date_default_timezone_get();
	date_default_timezone_set(get_option('timezone_string'));
	$date_str = date('Ymd');
	date_default_timezone_set($script_tz);

	$sh5vp_cache = secure_html5_video_player_parent_path_with_file(__FILE__, 'wp-config.php', 10) . '/wp-content/sh5vp_cache/';	
	$sh5vp_cache_index = $sh5vp_cache . 'index.php';
	if (!file_exists($sh5vp_cache_index)) {
		secure_html5_video_player_write_silence_file($sh5vp_cache_index);
	}
	
	$filename_normalized_ext = secure_html5_video_player_filename_normalized_ext($file . '.' . $ext);

	$video_orig = "{$secure_html5_video_player_video_dir}/{$file}.{$ext}";
	$video_cache_dir = $sh5vp_cache . $date_str . '/' . $access_key . '/';
	
	$video_cache = $video_cache_dir . $filename_normalized_ext;	
	$video_cache_dir_index = $sh5vp_cache . $date_str . '/' . 'index.php';
	$video_cache_dir_index2 = $video_cache_dir . 'index.php';
	$video_cache_dir_index3 = '';
	
	$last_slash_pos = strrpos($filename_normalized_ext, '/');
	if (last_slash_pos !== FALSE) {
		$video_cache_dir_index3 = $video_cache_dir . substr($filename_normalized_ext, 0, $last_slash_pos);
	}
	
	if (!is_dir($video_cache_dir)) {
		mkdir($video_cache_dir, 0777, TRUE);
	}
	if ($video_cache_dir_index3 != '' && !is_dir($video_cache_dir_index3)) {
		mkdir($video_cache_dir_index3, 0777, TRUE);	
	}
	
	if (!file_exists($video_cache_dir_index)) {
		secure_html5_video_player_write_silence_file($video_cache_dir_index);
	}
	if (!file_exists($video_cache_dir_index2)) {
		secure_html5_video_player_write_silence_file($video_cache_dir_index2);
	}

	if (!file_exists($video_cache) || abs(filesize($video_orig) - filesize($video_cache)) > 512) {
		file_get_contents("{$plugin_dir}/prepvideo.php?k=" . urlencode($access_key) . "&file=" . urlencode($file . '.' . $ext));
	}
	
	if (file_exists($video_cache)) {
		return content_url() . '/sh5vp_cache/' . $date_str . '/' . $access_key . '/' . $filename_normalized_ext;
	}
	return $dynamic_url;
}
endif;



if ( !function_exists('secure_html5_video_player_write_silence_file') ):
function secure_html5_video_player_write_silence_file($filepath) {
	$path_parts = pathinfo($filepath);
	$path_dir = $path_parts['dirname'];
	if (!is_dir($path_dir)) {
		mkdir($path_dir, 0777, TRUE);
	}
	$fp = fopen($filepath, 'w');
	fwrite($fp, "<?php \n// Silence is golden.\n?>");
	fclose($fp);
} 
endif;



if ( !function_exists('secure_html5_video_player_rrmdir') ):
function secure_html5_video_player_rrmdir($dir) {
	if (!is_dir($dir)) {
		unlink($dir);
		return;
	}
	$objects = scandir($dir);
	foreach ($objects as $object) {
		if ($object != "." && $object != "..") {
			if (filetype($dir."/".$object) == "dir") {
				secure_html5_video_player_rrmdir($dir."/".$object); 
			}
			else {
				unlink($dir."/".$object);
			}
		}
	}
	reset($objects);
	rmdir($dir);
} 
endif;



if ( !function_exists('secure_html5_video_player_footer_cleanup') ):
function secure_html5_video_player_footer_cleanup() {
	$script_tz = date_default_timezone_get();
	date_default_timezone_set(get_option('timezone_string'));
	$date_str = date('Ymd');
	$date_str_yesterday = date('Ymd', time() - 86400);
	date_default_timezone_set($script_tz);

	$secure_html5_video_player_serve_method = get_option('secure_html5_video_player_serve_method');
	$sh5vp_cache = secure_html5_video_player_parent_path_with_file(__FILE__, 'wp-config.php', 10) . '/wp-content/sh5vp_cache/';	
	$sh5vp_cache_ls = scandir($sh5vp_cache);
	foreach ($sh5vp_cache_ls as $currdir) {
		if ($currdir != '.' 
		&& $currdir != '..' 
		&& $currdir != 'index.php'
		&& is_numeric($currdir)
		&& ($secure_html5_video_player_serve_method == 'dynamic' 
			|| $currdir != $date_str && $currdir != $date_str_yesterday)) {
			secure_html5_video_player_rrmdir($sh5vp_cache . $currdir);			
		}		
	}
}
endif;



if ( !function_exists('secure_html5_video_player_admin_enqueue_scripts') ):
function secure_html5_video_player_admin_enqueue_scripts($hook) {
	$plugin_dir = plugins_url('secure-html5-video-player');
	wp_register_style( 'sh5vp-admin-style', $plugin_dir . '/sh5vp-admin.css');
	wp_enqueue_style( 'sh5vp-admin-style' );
	wp_enqueue_script( 'sh5vp-admin-js', $plugin_dir  . '/sh5vp-admin.js');
}
endif;



if ( !function_exists('secure_html5_video_player_can_play') ):
function secure_html5_video_player_can_play($has_mp4, $has_ogg, $has_webm) {
	$bd = SH5VP_BrowserDetect::detect();
	$can_play_mp4 = TRUE;
	$can_play_ogg = FALSE;
	$can_play_webm = FALSE;
	if ($bd->isChrome()) {
		$can_play_ogg = TRUE;
		$can_play_webm = TRUE;
	}
	elseif ($bd->isFirefox()) {
		if ($bd->versionFirefox() >= 21 && !$bd->isMac()) {
			$can_play_ogg = TRUE;
			$can_play_webm = TRUE;
		}
		else {
			$can_play_mp4 = FALSE;
			$can_play_ogg = TRUE;
			$can_play_webm = TRUE;
		}	
	}
	return ($has_mp4 && $can_play_mp4) || ($has_ogg && $can_play_ogg) || ($has_webm && $can_play_webm);
}
endif;

?>
