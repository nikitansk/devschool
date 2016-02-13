<section class="title-area">
<?php if(has_post_thumbnail()){ 
		$url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' ); 
	}else{
		$vibe = Wplms_Modern_Init::init();
		$url = $vibe->option('hero_img');
	}
	
	if(empty($url))
		$url = get_stylesheet_directory_uri().'/assets/images/default.jpeg';
	
?>
	<div class="title-content" style="background:url(<?php echo (is_Array($url)?$url[0]:$url); ?>) no-repeat 50% 50%; ">
		<div class="container">
			<div class="title-text">
				<div class="row">
					<div class="col-md-12">
						<?php
						echo '<h1>'.get_the_title().'</h1>';
						?>
					</div>
				</div>
			</div>
		</div>
	</div>	
</section>