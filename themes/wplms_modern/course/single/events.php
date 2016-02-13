<?php
/**
 * The template for displaying Course events
 *
 * Override this template by copying it to yourtheme/course/single/events.php
 *
 * @author 		VibeThemes
 * @package 	vibe-course-module/templates
 * @version     1.8.1
 */


global $post;
if(class_exists('WPLMS_Events_Interface')){
?>


<h3 class="heading"><?php the_title(); echo ' ';_e('Events','vibe')  ?></h1>
<br />
<?php
	    $events_interface = new WPLMS_Events_Interface;
		$events_interface->wplms_event_calendar(get_the_ID());
		
 }
?>