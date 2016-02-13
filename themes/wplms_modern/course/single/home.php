<?php 
	get_header( 'buddypress' );
	$vibe = Wplms_Modern_Init::init();
	$bg = get_post_meta(get_the_ID(),'vibe_course_bg',true);
	if(empty($bg)){
		$url = $vibe->option('hero_img');
	}else{
		$url = wp_get_attachment_image_src($bg,'full');
	}
?>
<?php if ( bp_course_has_items() ) : while ( bp_course_has_items() ) : bp_course_the_item(); ?>

<section id="content">
	<div id="buddypress">
		<div class="course_header">

			<?php do_action( 'bp_before_course_home_content' ); ?>

			<div id="item-header" role="complementary" <?php echo (empty($url)?'':'style="background:url('.(is_array($url)?$url[0]:$url).') 50% 50% no-repeat;"');?>> 

				<?php locate_template( array( 'course/single/course-header.php' ), true ); ?>

			</div><!-- #item-header -->
		</div>
		<div id="item-nav">
			<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
				<div class="container">
			        <div class="row">
			            <div class="col-md-9 col-md-offset-3">
							<ul>
								<?php bp_get_options_nav(); ?>
								<?php

								if(function_exists('bp_course_nav_menu'))
									bp_course_nav_menu();
								
								?>
								<?php do_action( 'bp_course_options_nav' ); ?>
							</ul>
						</div>
					</div><!-- #item-nav -->
				</div>
			</div>		
		</div>
	    <div class="container">
	        <div class="row" itemscope itemtype="http://schema.org/Product">
	           <div class="col-md-3 col-sm-4">	
	           <div class="course_essentials">
					<?php 
					bp_course_instructor();
					the_course_details(); ?>
				</div>
				<div class="students_undertaking">
					<?php
					$students_undertaking = $vibe->get_students_undertaking(array('number'=>9));
					$students=get_post_meta(get_the_ID(),'vibe_students',true);

					echo '<strong>'.$students.__(' STUDENTS ENROLLED','vibe').'</strong>';

					echo '<ul>';
					$i=0;
					foreach($students_undertaking as $student){
						$i++;
						echo '<li>'.get_avatar($student).'</li>';
						if($i>5)
							break;
					}
					echo '</ul>';
					?>
				</div>
			 	<?php
			 		$sidebar = apply_filters('wplms_sidebar','coursesidebar',get_the_ID());
	                if ( !function_exists('dynamic_sidebar')|| !dynamic_sidebar($sidebar) ) : ?>
               	<?php endif; ?>
			</div>
				<div class="col-md-9 col-sm-8">	
				<?php do_action( 'template_notices' ); ?>
				<div id="item-body">

					<?php 
					
					do_action( 'bp_before_course_body' );

					/**
					 * Does this next bit look familiar? If not, go check out WordPress's
					 * /wp-includes/template-loader.php file.
					 *
					 * @todo A real template hierarchy? Gasp!
					 */

					$current_action = bp_current_action();
	
					if(!empty($_GET['action'])){
						$current_action = $_GET['action'];
					}
					global $bp;
					if(!empty($current_action)):
						switch($current_action){
							case 'curriculum':
								locate_template( array( 'course/single/curriculum.php'  ), true );
							break;
							case 'members':
								locate_template( array( 'course/single/members.php'  ), true );
							break;
							case 'activity':
								locate_template( array( 'course/single/activity.php'  ), true );
							break;
							case 'submissions':
							case 'stats':
							case 'admin':
								$uid = bp_loggedin_user_id();
								$authors=array($post->post_author);
								$authors = apply_filters('wplms_course_instructors',$authors,$post->ID);
								
								if(current_user_can( 'manage_options' ) || in_array($uid,$authors)){
									locate_template( array( 'course/single/admin.php'  ), true );	
								}else{
									vibe_include_template("course/front$course_layout.php",'course/single/front.php');
								}
							break;
							case 'questions':
							case 'events':
							break;
							default:
								vibe_include_template("course/front$course_layout.php",'course/single/front.php');
							break;
						}
						do_action('wplms_load_templates');
					else :
						
						if ( isset($_POST['review_course']) && isset($_POST['review']) && wp_verify_nonce($_POST['review'],get_the_ID()) ){
							 global $withcomments;
						      $withcomments = true;
						      comments_template('/course-review.php',true);
						}else if(isset($_POST['submit_course']) && isset($_POST['review']) && wp_verify_nonce($_POST['review'],get_the_ID())){ // Only for Validation purpose
							
							bp_course_check_course_complete();
							
						// Looking at home location
						}else if ( bp_is_course_home() ){
							
							// Use custom front if one exists
							$custom_front = locate_template( array( 'course/single/front.php' ) );
							if     ( ! empty( $custom_front   ) ) : vibe_include_template("course/front$course_layout.php",'course/single/front.php');
							
							elseif ( bp_is_active( 'structure'  ) ) : locate_template( array( 'course/single/structure.php'  ), true );
							// Otherwise show members
							elseif ( bp_is_active( 'members'  ) ) : locate_template( array( 'course/single/members.php'  ), true );
							endif;
						// Not looking at home
						}else {
							
							// Course Admin/Instructor
							if     ( bp_is_course_admin_page() ) : locate_template( array( 'course/single/admin.php'        ), true );
								// Course Members
							elseif ( bp_is_course_members()    ) : locate_template( array( 'course/single/members.php'      ), true );
							// Anything else (plugins mostly)
							else                                : 
								locate_template( array( 'course/single/plugins.php'      ), true );
							endif;
						}
					endif;
						
					do_action( 'bp_after_course_body' ); ?>

				</div><!-- #item-body -->

				<?php do_action( 'bp_after_course_home_content' ); ?>

				</div>
			</div><!-- .padder -->
		
		</div><!-- #container -->
	</div>
</section>	
<?php endwhile; endif; ?>
<?php 
get_footer( 'buddypress' ); 