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

add_action('wp_head', 'secure_html5_video_player_add_header');
add_action('admin_menu', 'secure_html5_video_player_menu');
add_action('plugins_loaded', 'secure_html5_video_player_plugins_loaded');
add_action('admin_enqueue_scripts', 'secure_html5_video_player_admin_enqueue_scripts');

add_shortcode(
	get_option('secure_html5_video_player_video_shortcode', 'video'),
	'secure_html5_video_player_shortcode_video'
);


if ( !function_exists('secure_html5_video_player_menu') ):
function secure_html5_video_player_menu() {
	add_options_page(
		__('Secure HTML5 Video Player', 'secure-html5-video-player'),
		__('Secure HTML5 Video Player', 'secure-html5-video-player'),
		'manage_options',
		'secure-html5-video-player.php',
		'secure_html5_video_player_options'
	);
}
endif;

?>