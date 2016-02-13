<?php
/**
 * The template for displaying Course Header
 *
 * Override this template by copying it to yourtheme/course/single/header.php
 *
 * @author 		VibeThemes
 * @package 	vibe-course-module/templates
 * @version     1.8.1
 */

do_action( 'bp_before_course_header' );

?>
<div class="container">
	<div class="row">
		<div class="col-md-3">
			<div id="item-header-avatar">
					<?php bp_course_avatar(); ?>
			</div><!-- #item-header-avatar -->
		</div>
		<div class="col-md-6">
			<div id="item-header-content">
				<span class="highlight" itemprop="category"><?php bp_course_type(); ?></span>
				<h3><a href="<?php bp_course_permalink(); ?>" title="<?php bp_course_name(); ?>" itemprop="name"><?php bp_course_name(); ?></a></h3>
				
				<?php do_action( 'bp_before_course_header_meta' ); ?>

				<div id="item-meta">
					<?php bp_course_meta(); ?>
						<?php do_action( 'bp_course_header_actions' ); ?>

					<?php do_action( 'bp_course_header_meta' ); ?>

				</div>
			</div><!-- #item-header-content -->
		</div>	
		<div class="col-md-3">
			<div class="course_price">
			<?php bp_course_credits(); ?></div>	
			<?php the_course_button(); ?>
		</div>
		<?php
		do_action( 'bp_after_course_header' );
		?>
	</div>
</div>