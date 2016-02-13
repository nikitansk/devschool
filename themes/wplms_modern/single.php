<?php
/**
 * Single.php
 * @author 		VibeThemes
 * @category 	Admin
 * @package 	wplms_modern/single
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$vibe = Wplms_Modern_Init::init();

if(have_posts()):
	while(have_posts()):the_post();
?>
<section class="title-area">
	<?php 
	$url = $vibe->option('hero_img');
	if(empty($url))
		$url = get_stylesheet_directory_uri().'/assets/images/default.jpeg';
?>
	<div class="title-content" style="background:url(<?php echo (is_Array($url)?$url[0]:$url); ?>) no-repeat 50% 50%; ">
		<div class="container">
			<div class="title-text">
				<div class="row">
					<div class="col-md-12">
						<?php
						global $post;
						echo '<h1>';the_title();echo '</h1>';
						the_sub_title();
						?>
					</div>
				</div>
			</div>
		</div>
	</div>	
</section>

<section id="content">
	<div class="container">
		<div class="col-md-8 col-md-offset-2">
			<?php
			the_content();
			?>
			<?php comments_template(); ?>
		</div>
	</div>
</section>		
<?php
	endwhile;
endif;	
?>

<?php
$next_post = get_next_post();

if ( is_a( $next_post , 'WP_Post' ) ) { ?>
	<section class="more-title-area">
		<?php if(has_post_thumbnail($next_post->ID)){ 
			$url = wp_get_attachment_image_src( get_post_thumbnail_id($next_post->ID), 'full' ); 
		}else{
			$url = $vibe->option('hero_img');
		}
				?>
		<div class="more-title-content" style="background:url(<?php echo $url[0]; ?>) no-repeat 50% 50%; ">
			<div class="container">
				<div class="more-title-text">
					<div class="row">
						<div class="col-md-12">
							<h2><a href="<?php echo get_permalink( $next_post->ID ); ?>"><?php echo get_the_title( $next_post->ID ); ?></a></h1>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</section>
  
<?php } ?>
<?php
get_footer();
