<?php

/**
 * BuddyPress - Users Plugins
 *
 * This is a fallback file that external plugins can use if the template they
 * need is not installed in the current theme. Use the actions in this template
 * to output everything your plugin needs.
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

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
	        	<div class="col-md-3 col-sm-4">
	        		<div class="buddypress_sidebar">
		        		<?php
		        		global $bp;
	                    $sidebar = apply_filters('wplms_sidebar','member',$bp->displayed_user->id);
	                    if ( !function_exists('dynamic_sidebar')|| !dynamic_sidebar($sidebar) ) : ?>
	                    <?php endif; ?>
                    </div>
	        	</div>
				<div class="col-md-9 col-sm-8">
					<div class="padder">
						<div id="item-body">

							<?php do_action( 'bp_before_member_body' ); ?>

							<div class="item-list-tabs no-ajax" id="subnav">
								<ul>

									<?php bp_get_options_nav(); ?>

									<?php do_action( 'bp_member_plugin_options_nav' ); ?>

								</ul>
							</div><!-- .item-list-tabs -->

							<h3><?php do_action( 'bp_template_title' ); ?></h3>

							<?php do_action( 'bp_template_content' ); ?>

							<?php do_action( 'bp_after_member_body' ); ?>

						</div><!-- #item-body -->


					<?php do_action( 'bp_after_member_plugin_template' ); ?>

					</div><!-- .padder -->
				</div>
			</div>
		</div>
	</div>
</section>	<!-- #content -->
</div> <!-- Extra Global div in header -->									
<?php get_footer( 'wplms_modern' ); ?>
