<?php


$vibe = Wplms_Modern_Init::init();
if (isset($_GET["post_type"]) && $_GET["post_type"] == 'course'){ 
    load_template(get_stylesheet_directory() . '/search-incourse.php'); 
    exit();
}

get_header();
global $wp_query;
$total_results = $wp_query->found_posts;
$url = $vibe->option('hero_img');
?>

<section class="title-area" <?php ?>>
	<div class="title-content" style="background:url(<?php echo (empty($url)?get_stylesheet_directory_uri().'/assets/images/default.jpeg':$url); ?>)">
		<div class="container">
			<div class="title-text">
				<div class="row">
					<div class="col-md-12">
					 	<h1><?php _e('Search Results for "', 'wplms_modern'); the_search_query(); ?>"</h1>
                		<h5><?php echo $total_results.__(' results found','wplms_modern');  ?></h5>
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
			
			 if ( have_posts() ) : while ( have_posts() ) : the_post();
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
					
				endwhile;
				pagination();
			endif;
		?>
		</div>
	</div>
</section>



<?php
get_footer();
