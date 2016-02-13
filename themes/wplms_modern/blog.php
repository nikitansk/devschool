<?php
/*
Template Name: Blog
*/


$vibe = Wplms_Modern_Init::init();
get_header();
?>

<section class="title-area">
	<?php if(has_post_thumbnail()){ 
		$url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' ); 
	}else{
		$url = $vibe->option('hero_img');
	}
	$url = $vibe->option('hero_img');
	?>
	<div class="title-content" style="background:url(<?php echo (empty($url)?get_stylesheet_directory_uri().'/assets/images/default.jpeg':$url); ?>)">
		<div class="container">
			<div class="title-text">
				<div class="row">
					<div class="col-md-12">
						<?php
						echo '<h1>'.get_the_title().'</h1>';
						the_sub_title();
						?>
					</div>
				</div>
			</div>
		</div>
	</div>	
</section>

<section>
	<div class="container">
		<div class="col-md-8 col-md-offset-2">
		<?php
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

			$args = array(
				'post_type' => 'post',
				'paged' => $paged
				);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					?>
					<div class="blogpost">
						<div class="post_content">
							<div class="postmeta">
								<ul>
								<li><?php echo sprintf('%02d', get_the_time('j')).' / '.get_the_time('M').' / '.get_the_time('y');?></li>
								<li><a href="'<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_the_author_meta( 'display_name' ); ?></a></li>
								<li><?php echo get_comments_number().' comments'; ?></li>
								</ul>
								<?php echo get_the_category_list(); ?>
							</div>	
							<h3><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php the_excerpt(); ?>
						</div>
					</div>
					<?php
					
				}
				
			} 
			
			wp_reset_postdata();
			pagination($the_query->max_num_pages,4);
        
		?>
		</div>
	</div>
</section>



<?php
get_footer();
