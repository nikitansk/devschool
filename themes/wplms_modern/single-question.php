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
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="content">
                    <div class="main_content">
                    <?php
                        the_question();
                    ?>
                    </div>
                    <?php
                    do_action('wplms_question_after_content');
                    ?>
                </div>
                <?php
                endwhile;
                endif;

                do_action('wplms_front_end_question_controls');
                ?>
            </div>
        </div>
    </div>
</section>		
<?php

get_footer();
