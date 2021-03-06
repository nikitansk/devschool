<?php
/**
 * The template for displaying Quiz/Assignment/Coruse submissions in course admin
 *
 * Override this template by copying it to yourtheme/course/single/submissions.php
 *
 * @author 		VibeThemes
 * @package 	vibe-course-module/templates
 * @version     2.0
 */

$course_id=get_the_ID();
global $wpdb;


$tabs = apply_filters('wplms_course_submission_tabs',array(
	'quiz' => sprintf(_x('Quiz Submissions <span>%d</span>','Quiz Submissions in course/admin/submissions','vibe'),bp_course_get_quiz_submission_count($course_id)),
	'course' => sprintf(_x('Course Submissions <span>%d</span>','Course Submissions in course/admin/submissions','vibe'),bp_course_get_course_submission_count($course_id)),
	),$course_id);

?>

<div class="submissions unit_content">
  <ul class="nav nav-tabs">
  	<?php
  		$i=0;
  		foreach($tabs as $component => $label){
  	?>
  		<li <?php echo (empty($i)?'class="active"':''); ?>><a href="#<?php echo $component; ?>" data-toggle="tab"><?php echo $label; ?></a></li>
  	<?php
	  		$i++; 
	  	}
  	?>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
  	<?php
  		$i=0;
  		foreach($tabs as $component => $label){
  	?>
    <div class="tab-pane <?php echo (empty($i)?'active':''); ?>" id="<?php echo $component; ?>">
    	<?php 
    		$hook = 'wplms_course_submission_'.$component.'_tab_content';
    		do_action($hook,$course_id); 
    	?>
    </div>
    <?php
	  		$i++; 
	  	}
  	?>
  </div>
  <div id="custom_editor" style="display:none;"> 
      <?php
          $content = '';
          $editor_id = 'mycustomeditor';

          wp_editor( $content, $editor_id ); 
          ?>
  </div>
</div>
<?php
