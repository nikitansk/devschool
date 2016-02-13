<?php get_header( 'wplms_modern' ); ?>

<section class="title-area">
<?php 
		$vibe = Wplms_Modern_Init::init();
		$url = $vibe->option('hero_img');

?>
	<div class="title-content" style="background:url(<?php echo (is_Array($url)?$url[0]:$url); ?>) no-repeat 50% 50%; ">
		<div class="container">
			<div class="title-text">
				<div class="row">
					<div class="col-md-12">
						<h1><?php single_cat_title(); ?></h1>
                    	<h5><?php echo category_description(); ?></h5>
					</div>
				</div>
			</div>
		</div>
	</div>	
</section>
<section id="content">
	<div id="buddypress">
    <div class="container">
		<div class="padder">
		<?php do_action( 'bp_before_directory_course' ); ?>	
		<div class="row">
			<div class="col-md-9 col-sm-8">
				<div class="content padding_adjusted">
				<?php
					if ( have_posts() ) : while ( have_posts() ) : the_post();

					echo '<div class="col-md-4 col-sm-6 clear3">'.thumbnail_generator($post,'modern1','3','0',true,true).'</div>';
				
					endwhile;
					pagination();
					endif;
				?>
				</div>
			</div>	
			<div class="col-md-3 col-sm-3">
				<?php
                    $sidebar = apply_filters('wplms_sidebar','wplms_modern',get_the_ID());
                    if ( !function_exists('dynamic_sidebar')|| !dynamic_sidebar($sidebar) ) : ?>
                <?php endif; ?>
			</div>
		</div>	
		<?php do_action( 'bp_after_directory_course' ); ?>

		</div><!-- .padder -->
	
	<?php do_action( 'bp_after_directory_course_page' ); ?>
</div><!-- #content -->
</div>
</section>

<?php get_footer( 'wplms_modern' ); ?>