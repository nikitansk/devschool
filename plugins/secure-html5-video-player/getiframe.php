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

?><!DOCTYPE html><html>
<head>
<?php secure_html5_video_player_add_header(); ?>
<style>
html, body, iframe, video, object, div {
	border: 0;
	margin: 0;
	outline: 0;
	padding: 0;
}
video, object, iframe {
	width:100%;
	height:100%;
	min-width:100%;
	min-height:100%;
	max-width:100%;
	max-height:100%;
	position:absolute;
	top:0px;
	left:0px;
	background-color:#000;
}
.sh5vp-download-links {
	display:none;
}
</style>
</head>
<body>
<?php

$access_key = secure_html5_video_player_accessKey($filename);
$access_key2 = secure_html5_video_player_accessKey('preview-iframe');
if ($_GET['k'] != $access_key && $_GET['k'] != $access_key2) {
	print 'Access Denied';
}
else {
	$secure_html5_video_player_video_shortcode = get_option('secure_html5_video_player_video_shortcode', 'video');
	print do_shortcode(
		'['.$secure_html5_video_player_video_shortcode.' file="'.$_GET['file'].'" '
		.' youtube="'.$_GET['youtube'].'" vimeo="'.$_GET['vimeo'].'" '
		.' preload="'.$_GET['preload'].'" autoplay="'.$_GET['autoplay'].'" loop="'.$_GET['loop'].'" ]'
	);
}
?>
</body>
</html>