<?php 

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


define('WP_USE_THEMES', false);
define( 'ABSPATH', secure_html5_video_player_parent_path_with_file(__FILE__, 'wp-config.php', 10) . '/');

require_once( ABSPATH . 'wp-config.php' );
require_once('sh5vp-functions.php');

$filename = $_GET['file'];
$filename_no_ext = secure_html5_video_player_filename_no_ext($_GET['file']);
$access_key = secure_html5_video_player_accessKey($filename_no_ext);
if ($_GET['k'] != $access_key) {
	exit();
}


if ($_GET['onlyclean'] != '1') {	
	$secure_html5_video_player_video_dir = get_option('secure_html5_video_player_video_dir');
	$video_orig = "{$secure_html5_video_player_video_dir}/{$filename}";
	if (!file_exists($video_orig)) {
		exit();
	}
	
	ignore_user_abort(true);
	set_time_limit(3600);	
	ob_start();
	echo 1;
	header('Content-Type: text/plain');
	header('Connection: close');
	header('Content-Length: '.ob_get_length());
	ob_end_flush();
	ob_flush();
	flush();
	if (session_id()) session_write_close();
	
	$script_tz = date_default_timezone_get();
	date_default_timezone_set(get_option('timezone_string'));
	$date_str = date('Ymd');
	date_default_timezone_set($script_tz);
	
	$sh5vp_cache = secure_html5_video_player_parent_path_with_file(__FILE__, 'wp-config.php', 10) . '/wp-content/sh5vp_cache/';	
	$sh5vp_cache_index = $sh5vp_cache . 'index.php';
	if (!file_exists($sh5vp_cache_index)) {
		secure_html5_video_player_write_silence_file($sh5vp_cache_index);
	}
	
	$video_cache_dir = $sh5vp_cache . $date_str . '/' . $access_key . '/';
	$filename_normalized_ext = secure_html5_video_player_filename_normalized_ext($filename);
	
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
	
	$in_progress_file = $video_cache . '.busy';
	if (!file_exists($video_cache) 
	|| abs(filesize($video_orig) - filesize($video_cache)) > 512 && !file_exists($in_progress_file)) {
	
		$fp = fopen($in_progress_file, 'w');
		fwrite($fp, "1");
		fclose($fp);
		
		$secure_html5_video_player_serve_method = get_option('secure_html5_video_player_serve_method');
		if ($secure_html5_video_player_serve_method == 'link') {
			if (!symlink($video_orig, $video_cache)) {
				copy($video_orig, $video_cache);
			}
		}
		else {
			if (!link($video_orig, $video_cache)) {
				copy($video_orig, $video_cache);
			}
		}
		unlink($in_progress_file);
	}

}

secure_html5_video_player_footer_cleanup();

?>
