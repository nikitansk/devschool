<?php

/**
 * WPLMS- DASHBOARD TEMPLATE
 */

if(!is_user_logged_in()){
	wp_redirect(home_url(),'302');
}
?>
<?php get_header( 'wplms_modern' ); ?>


<section id="content">
	<div id="buddypress">
		<div class="member_header">
			<?php

			/**
			 * Fires before the display of member home content.
			 *
			 * @since BuddyPress (1.2.0)
			 */
			do_action( 'bp_before_member_home_content' ); ?>

			<div id="item-header" role="complementary">
				<?php bp_get_template_part( 'members/single/member-header' ) ?>
			</div>
		</div><!-- #item-header -->
		<div id="item-nav" class="">
			<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
				<div class="container">
					<div class="col-md-9 col-md-offset-3">
						<ul>

							<?php bp_get_displayed_user_nav(); ?>

							<?php do_action( 'bp_member_options_nav' ); ?>

						</ul>
					</div>
				</div>
			</div>
		</div><!-- #item-nav -->
		<div class="container">
	        <div class="row">
				<div class="col-md-12">
					<div class="padder">
						<div class="wplms-dashboard row">
							<?php do_action( 'bp_before_dashboard_body' ); ?>
							<?php
								if(current_user_can('edit_posts')){
									$sidebar = apply_filters('wplms_instructor_sidebar','instructor_sidebar');
				                    if ( !function_exists('dynamic_sidebar')|| !dynamic_sidebar($sidebar) ) : endif; 
								}else{
				                    $sidebar = apply_filters('wplms_student_sidebar','student_sidebar');
				                    if ( !function_exists('dynamic_sidebar')|| !dynamic_sidebar($sidebar) ) : endif; 
								}
							?>
							<?php do_action( 'bp_after_dashboard_body' ); ?>
						</div>	<!-- .wplms-dashbaord -->
					</div><!-- .padder -->

					<?php do_action( 'bp_after_member_dashboard_template' ); ?>

					</div>
				</div><!-- #content -->
			</div>
		</div>
</section>	
</div> <!-- Extra Global div in header -->									
<?php get_footer( 'wplms_modern' ); ?>
