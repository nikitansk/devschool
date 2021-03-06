<?php

/**
 * BuddyPress - Members Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */
$flag=1;
$members_view=vibe_get_option('members_view');

if(isset($members_view) && $members_view){
	$flag=0;
	switch($members_view){
		case 1:
			if(is_user_logged_in())$flag=1;
		break;
		case 2:
			if(current_user_can('edit_posts'))$flag=1;
		break;
		case 3:
			if(current_user_can('manage_options'))$flag=1;
		break;
	}
}

if(!$flag){
	$id=vibe_get_option('members_redirect'); 
	if(isset($id))
		wp_redirect(get_permalink($id));
	exit();
}
get_header( 'wplms_modern' ); 



$vibe = Wplms_Modern_Init::init();
$id=0;
$page_array=get_option('bp-pages');
if(isset($page_array['members'])){
	$id = $page_array['members'];
}

 if(has_post_thumbnail($id)){ 
	$url = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'full' ); 
	$url = $url[0];
}else{
	$url = $vibe->option('hero_img');
}
?>
<section id="coursestitle" style="background:url(<?php echo (empty($url)?get_stylesheet_directory_uri().'/assets/images/default.jpeg':$url); ?>)">
    <div class="container">
        <div class="pagetitle">
        	<h1><?php echo get_the_title($id); ?></h1>
            <?php the_sub_title($id); ?>
        </div>
    </div>
</section>

<section id="content">
	<div id="buddypress">
    <div class="container">
		<?php do_action( 'bp_before_directory_members_page' ); ?>
		<div class="padder">
			<?php do_action( 'bp_before_directory_members' ); ?>
				<form action="" method="post" id="members-directory-form" class="dir-form">
				<div class="row">	
						<div class="col-md-3 col-sm-4">
							<?php do_action( 'bp_before_directory_members_tabs' ); ?>
							<div class="item-list-tabs" role="navigation">
								<ul>
									<li class="selected" id="members-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_members_root_slug() ); ?>"><?php printf( __( 'All Members <span>%s</span>', 'wplms_modern' ), bp_get_total_member_count() ); ?></a></li>

									<?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

										<li id="members-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/' ?>"><?php printf( __( 'My Friends <span>%s</span>', 'wplms_modern' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>

									<?php endif; ?>
									
									<?php do_action( 'bp_members_directory_member_types' ); ?>

								</ul>
							</div><!-- .item-list-tabs -->
							<?php 
			            		do_action('wplms_be_instructor_button');	
							?>
						<div id="member-dir-search" class="dir-search" role="search">
							<?php bp_directory_members_search_form(); ?>
						</div><!-- #group-dir-search -->
						<?php
			                if ( !function_exists('dynamic_sidebar')|| !dynamic_sidebar('buddypress') ) : ?>
		               	<?php endif; ?>
		               	
					</div>

						
					<div class="col-md-9 col-sm-8">	
						
						<div class="item-list-tabs" id="subnav" role="navigation">
							<ul>
								
								<?php do_action( 'bp_members_directory_member_sub_types' ); ?>
								<li class="switch_view"><a id="list_view" class="active"><i class="icon-list-1"></i></a><a id="grid_view"><i class="icon-grid"></i></a>
								</li>
								<li id="members-order-select" class="last filter">

									<label for="members-order-by"><?php _e( 'Order By:', 'wplms_modern' ); ?></label>
									<select id="members-order-by">
									<?php $default_selection = apply_filters('wplms_members_default_order',''); ?>
										<option value="active" <?php selected('active',$default_selection); ?>><?php _e( 'Last Active', 'wplms_modern' ); ?></option>
										<option value="newest" <?php selected('newest',$default_selection); ?>><?php _e( 'Newest Registered', 'wplms_modern' ); ?></option>

										<?php if ( bp_is_active( 'xprofile' ) ) : ?>

											<option value="alphabetical" <?php selected('alphabetical',$default_selection); ?>><?php _e( 'Alphabetical', 'wplms_modern' ); ?></option>

										<?php endif; ?>

										<?php do_action( 'bp_members_directory_order_options' ); ?>

									</select>
								</li>
							</ul>
						</div>

						<div id="members-dir-list" class="members dir-list">

							<?php locate_template( array( 'members/members-loop.php' ), true ); ?>

						</div><!-- #members-dir-list -->

						<?php do_action( 'bp_directory_members_content' ); ?>

						<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

						<?php do_action( 'bp_after_directory_members_content' ); ?>
					</div>	
				</div>	
			</form><!-- #members-directory-form -->
		<?php do_action( 'bp_after_directory_members' ); ?>
		</div><!-- .padder -->
	</div><!-- #content -->
	<?php do_action( 'bp_after_directory_members_page' ); ?>
	</div>
</section>	
</div>
<?php get_footer( 'wplms_modern' ); ?>
