<?php
/*
Plugin Name: Secure HTML5 Video Player
Plugin URI: http://www.trillamar.com/webcraft/secure-html5-video-player/
Description: Secure HTML5 Video Player allows you to play HTML5 video on modern browsers. Videos can be served privately; pseudo-streamed from a secured directory or via S3.
Author: Lucinda Brown, Jinsoo Kang
Version: 3.14
Author URI: http://www.trillamar.com/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/*
	Copyright (c) 2011 Lucinda Brown <info@trillamar.com>
	Copyright (c) 2011 Jinsoo Kang <info@trillamar.com>

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

$secure_html5_video_player_cache_ttl = 180;


require_once('sh5vp-browser-detect.php');
require_once('sh5vp-functions.php');
require_once('sh5vp-widgets.php');
require_once('sh5vp-metabox.php');
require_once('sh5vp-init.php');

register_activation_hook(__FILE__, 'secure_html5_video_player_install');

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'secure_html5_video_player_plugin_action_links', 10, 2);

/**
	Selects a media server from the list of available media servers using the client
	web browser's IP address, and the requested video file name, as parameters used to
	select the server.  In an ideal situation, this function should be overrided to
	provide media server best positioned to serve the specified IP address by a combination
	of server load, available bandwidth, and physical proximity to the client.
	returns the address the of the plugin directory on the remote server.

	To override this function, define a new function that has as arguments:
	$client_ip and $video_filename
	and returns a server address with the full URL path to the secure-html5-video-player
	installation.  Then use add_filter to register the function with wordpress.  For example,
	If the function was named my_function, then it would be registered by calling the following
	in your Wordpress template's functions.php:

		add_filter('secure_html5_video_player_get_media_server_address', 'my_function', 10, 2);
*/
if ( !function_exists('secure_html5_video_player_get_media_server_address') ):
function secure_html5_video_player_get_media_server_address($client_ip, $video_filename) {
	$has_media_server = secure_html5_video_player_has_media_server();
	if ($has_media_server) {
		$server_list = secure_html5_video_player_media_server_address_list();
		$chksum = crc32($client_ip);
		if ($chksum < 0) $chksum = -1 * $chksum;

		if ($video_filename) {
			$server_filelist = secure_html5_video_player_filelist(true);
			$server_list_with_file = $server_filelist[$video_filename];
			if (! empty($server_list_with_file)) {
				$server_list = $server_list_with_file;
			}
		}

		$num_servers = count($server_list);
		$selected_server = $chksum % $num_servers;
		if ($selected_server < $num_servers
		&& isset($server_list[$selected_server])
		&& $server_list[$selected_server]) {
			return $server_list[$selected_server];
		}
	}
	$plugin_url = plugins_url('secure-html5-video-player');
	return $plugin_url;
}
add_filter('secure_html5_video_player_get_media_server_address',
	'secure_html5_video_player_get_media_server_address', 1, 2);
endif;



?>
