<?php

/*
	Copyright (c) 2011-2014 Lucinda Brown <info@trillamar.com>
	Copyright (c) 2011-2014 Jinsoo Kang <info@trillamar.com>

	Secure HTML5 Video Player is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


add_action('add_meta_boxes', 'secure_html5_video_player_add_custom_box');
add_action('save_post', 'secure_html5_video_player_save_postdata');



if ( !function_exists('secure_html5_video_player_add_custom_box') ):
function secure_html5_video_player_add_custom_box() {
	$post_types = get_post_types('','names');
	foreach ($post_types as $post_type ) {
		add_meta_box(
			'secure_html5_video_player_metabox',
			__('Secure HTML5 Video Player', 'secure-html5-video-player'),
			'secure_html5_video_player_inner_custom_box',
			$post_type
		);
	}
}
endif;



if ( !function_exists('secure_html5_video_player_inner_custom_box') ):
function secure_html5_video_player_inner_custom_box($post) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'secure_html5_video_player_noncename' );
	$secure_html5_video_player_video_shortcode = get_option('secure_html5_video_player_video_shortcode', 'video');
	$defaults = array(
		'width' => get_option('secure_html5_video_player_default_width'),
		'height' => get_option('secure_html5_video_player_default_height'),
		'preload' => get_option('secure_html5_video_player_default_preload'),
		'autoplay' => get_option('secure_html5_video_player_default_autoplay'),
		'loop' => get_option('secure_html5_video_player_default_loop')
	);
	$instance = array(
		'video' => get_post_meta($post->ID, 'sh5vp-video', true),
		'youtube_video_id' => get_post_meta($post->ID, 'sh5vp-youtube_video_id', true),
		'vimeo_video_id' => get_post_meta($post->ID, 'sh5vp-vimeo_video_id', true),
		'width' => get_post_meta($post->ID, 'sh5vp-width', true),
		'height' => get_post_meta($post->ID, 'sh5vp-height', true),
		'preload' => get_post_meta($post->ID, 'sh5vp-preload', true),
		'autoplay' => get_post_meta($post->ID, 'sh5vp-autoplay', true),
		'loop' => get_post_meta($post->ID, 'sh5vp-loop', true)
	);
	foreach ($defaults as $key => $value) {
		if (!$instance[$key]) {
			$instance[$key] = $value;			
		}
	}
?><table>
<tr>
	<td><label for="sh5vp-video"><?php
		_e('Video', 'secure-html5-video-player');
	?>:</label></td>
	<td>
		<?php
		$video_files = secure_html5_video_player_filelist(true);
		if (! empty($video_files)) {
			$preview_key = secure_html5_video_player_accessKey('preview-iframe');

			?><select id="sh5vp-video" name="sh5vp-video" >
			<option value=""></option>
<?php
			foreach ($video_files as $curr_video_file => $server_addr) {
				?><option data-key="<?php print $preview_key; ?>" value="<?php print $curr_video_file; ?>" <?php
					if ($instance['video'] == $curr_video_file) {
						?> selected="selected" <?php
					}
				?> ><?php
					print $curr_video_file;
					//if (count($server_addr) > 0) {
					//	print ' (' . implode(', ', $server_addr) . ')';
					//}
				?></option><?php
			}
			?></select><?php
		}
		else {
			?><input type="text" id="sh5vp-video" name="sh5vp-video" value="<?php print $instance['video']; ?>" /><?php
		}
	?></td>
</tr>

<tr>
	<td><label for="sh5vp-youtube_video_id"><?php
		_e('Youtube video ID', 'secure-html5-video-player');
	?>:</label></td>
	<td><input type="text" id="sh5vp-youtube_video_id" name="sh5vp-youtube_video_id" value="<?php print $instance['youtube_video_id']; ?>" /></td>
</tr>

<tr>
	<td><label for="sh5vp-vimeo_video_id"><?php
		_e('Vimeo video ID', 'secure-html5-video-player');
	?>:</label></td>
	<td><input type="text" id="sh5vp-vimeo_video_id" name="sh5vp-vimeo_video_id" value="<?php print $instance['vimeo_video_id']; ?>" /></td>
</tr>

<tr>
	<td><label for="sh5vp-width"><?php
		_e('Width', 'secure-html5-video-player')
	?>:</label></td>
	<td><input type="text" id="sh5vp-width" name="sh5vp-width" value="<?php print $instance['width']; ?>" size="5" /> px</td>
</tr>
<tr>
	<td><label for="sh5vp-height"><?php
		_e('Height', 'secure-html5-video-player')
	?>:</label></td>
	<td><input type="text" id="sh5vp-height" name="sh5vp-height" value="<?php print $instance['height']; ?>" size="5"  /> px</td>
</tr>
<tr>
	<td></td>
	<td>
		<input type="checkbox" id="sh5vp-preload" name="sh5vp-preload" value="yes" <?php
	if ($instance['preload'] == 'yes') {
		?> checked="checked" <?php
	}
	?> />
		<label for="sh5vp-preload"><?php
		_e('Preload', 'secure-html5-video-player')
		?></label>
	</td>
</tr>
<tr>
	<td></td>
	<td>
		<input type="checkbox" id="sh5vp-autoplay" name="sh5vp-autoplay" value="yes" <?php
	if ($instance['autoplay'] == 'yes') {
		?> checked="checked" <?php
	}
	?> />
		<label for="sh5vp-autoplay"><?php
		_e('Autoplay', 'secure-html5-video-player')
		?></label>
	</td>
</tr>
<tr>
	<td></td>
	<td>
		<input type="checkbox" id="sh5vp-loop" name="sh5vp-loop" value="yes" <?php
	if ($instance['loop'] == 'yes') {
		?> checked="checked" <?php
	}
	?> />
		<label for="sh5vp-loop"><?php
		_e('Loop', 'secure-html5-video-player')
		?></label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="button" class="button-secondary" 
		value="<?php _e('Insert Into Post', 'secure-html5-video-player') ?>" 
		onclick="sh5vp_insertIntoPost();"/>
		<input type="button" class="button-secondary" 
		value="<?php _e('Preview Video', 'secure-html5-video-player') ?>" 
		onclick="sh5vp_previewVideo();"/>	
	</td>
</tr>
</table>

<div class="sh5vp-video-modal" id="sh5vp-video-modal" 
	data-video-shortcode="<?php echo $secure_html5_video_player_video_shortcode; ?>" 
	data-youtube_override_type="<?php echo get_option('secure_html5_video_player_youtube_override_type'); ?>"
	data-plugin_dir="<?php echo plugins_url('secure-html5-video-player'); ?>"
<?php 
	foreach ($defaults as $key => $value) {
		echo " data-default-{$key}=\"${value}\"";
	}
?> >
	<div class="sh5vp-modal-bg"></div>
	<div class="sh5vp-modal-inner">
		<div class="sh5vp-modal-close"></div>
		<h1 class="sh5vp-modal-title">Secure HTML5 Video Preview</h1>
		<iframe class="sh5vp-modal-iframe" width="560" height="315" src="" frameborder="0" allowfullscreen scrolling="no"></iframe>
		<div>
			<table>
				<tr><td class="sh5vp-preview-label">Video:</td><td class="sh5vp-preview-selected-video"></td></tr>
				<tr><td class="sh5vp-preview-label">Youtube:</td><td class="sh5vp-preview-selected-youtube"></td></tr>
				<tr><td class="sh5vp-preview-label">Vimeo:</td><td class="sh5vp-preview-selected-vimeo"></td></tr>
			</table>
		</div>
	</div>
</div>

<p><?php
		_e('To use the video in your template, call the function: <code>get_sh5vp_featured_video($post_id, $width, $height)</code>, which returns the appropriate video tag.  Or call: <code>sh5vp_featured_video($post_id, $width, $height)</code> which prints the appropriate video tag.  The arguments: <code>$width</code> and <code>$height</code> are optional, and taken from the settings above if not specified.', 'secure-html5-video-player')
?></p>
<p><?php
		_e('Alternatively, you may insert the video into the post as a shortcode by placing the text cursor into the editor and pressing the <em>Insert Into Post</em> button, above.', 'secure-html5-video-player')
?></p>
<?php
}
endif;



if ( !function_exists('secure_html5_video_player_save_postdata') ):
function secure_html5_video_player_save_postdata( $post_id ) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (!wp_verify_nonce($_POST['secure_html5_video_player_noncename'], plugin_basename( __FILE__ ))) {
		return;
	}
	// Check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return;
		}
	}
	else {
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
	}
	$save_preload = $_POST['sh5vp-preload'];
	$save_autoplay = $_POST['sh5vp-autoplay'];
	$save_loop = $_POST['sh5vp-loop'];
	if (!$save_preload) $save_preload = 'no';
	if (!$save_autoplay) $save_autoplay = 'no';
	if (!$save_loop) $save_loop = 'no';
	if (!$_POST['sh5vp-video'] && !$_POST['sh5vp-youtube_video_id'] && !$_POST['sh5vp-vimeo_video_id']) {
		delete_post_meta($post_id, "sh5vp-video");
		delete_post_meta($post_id, "sh5vp-youtube_video_id");
		delete_post_meta($post_id, "sh5vp-vimeo_video_id");
		delete_post_meta($post_id, "sh5vp-width");
		delete_post_meta($post_id, "sh5vp-height");
		delete_post_meta($post_id, "sh5vp-preload");
		delete_post_meta($post_id, "sh5vp-autoplay");
		delete_post_meta($post_id, "sh5vp-loop");
	}
	else {
		update_post_meta($post_id, "sh5vp-video", $_POST['sh5vp-video']);
		update_post_meta($post_id, "sh5vp-youtube_video_id", $_POST['sh5vp-youtube_video_id']);
		update_post_meta($post_id, "sh5vp-vimeo_video_id", $_POST['sh5vp-vimeo_video_id']);
		update_post_meta($post_id, "sh5vp-width", $_POST['sh5vp-width']);
		update_post_meta($post_id, "sh5vp-height", $_POST['sh5vp-height']);
		update_post_meta($post_id, "sh5vp-preload", $save_preload);
		update_post_meta($post_id, "sh5vp-autoplay", $save_autoplay);
		update_post_meta($post_id, "sh5vp-loop", $save_loop);
	}
}
endif;



if ( !function_exists('get_sh5vp_featured_video') ):
function get_sh5vp_featured_video($post_id, $arg_width = -1, $arg_height = -1) {
	$instance = array(
		'video' => get_post_meta($post_id, 'sh5vp-video', true),
		'youtube_video_id' => get_post_meta($post_id, 'sh5vp-youtube_video_id', true),
		'vimeo_video_id' => get_post_meta($post_id, 'sh5vp-vimeo_video_id', true),
		'width' => get_post_meta($post_id, 'sh5vp-width', true),
		'height' => get_post_meta($post_id, 'sh5vp-height', true),
		'preload' => get_post_meta($post_id, 'sh5vp-preload', true),
		'autoplay' => get_post_meta($post_id, 'sh5vp-autoplay', true),
		'loop' => get_post_meta($post_id, 'sh5vp-loop', true)
	);
	if ($arg_width > 0) {
		$instance['width'] = $arg_width;
	}
	if ($arg_height > 0) {
		$instance['height'] = $arg_height;
	}
	$instance['video'] = trim($instance['video']);
	$instance['youtube_video_id'] = trim($instance['youtube_video_id']);
	$instance['vimeo_video_id'] = trim($instance['vimeo_video_id']);
	if (!$instance['video'] && !$instance['youtube_video_id'] && !$instance['vimeo_video_id']) {
		return '';
	}
	$secure_html5_video_player_video_shortcode = get_option('secure_html5_video_player_video_shortcode', 'video');
	return do_shortcode(
		'['.$secure_html5_video_player_video_shortcode.' file="'.$instance['video'].'" '
		.' youtube="'.$instance['youtube_video_id'].'" vimeo="'.$instance['vimeo_video_id'].'" '
		.' preload="'.$instance['preload'].'" autoplay="'.$instance['autoplay'].'" loop="'.$instance['loop'].'" '
		.' width="'.$instance['width'].'" height="'.$instance['height'].'"]'
	);
}
endif;



if ( !function_exists('sh5vp_featured_video') ):
function sh5vp_featured_video($post_id, $arg_width = -1, $arg_height = -1) {
	print get_sh5vp_featured_video($post_id, $arg_width, $arg_height);
}
endif;



?>
