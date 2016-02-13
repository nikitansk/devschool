
<?php get_header( 'wplms_modern' ); ?>
<section id="content">
	<div id="buddypress">
		<div class="group_header">
			<?php 
			/**
			 * Fires before the display of member home content.
			 *
			 * @since BuddyPress (1.2.0)
			 */
			do_action( 'bp_before_group_home_content' ); ?>

			<div id="item-header" role="complementary">

				<?php locate_template( array( 'groups/single/group-header.php' ), true );?>

			</div><!-- #item-header -->
		</div><!-- #item-header -->
		<div id="item-nav">
			<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
				<div class="container">
					<div class="col-md-9 col-md-offset-3">
						<ul>

							<?php bp_get_options_nav(); ?>

							<?php do_action( 'bp_group_options_nav' ); ?>

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
						<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>
						<div id="item-body">

						<?php do_action( 'bp_before_group_body' ); ?>

						<?php do_action( 'bp_template_content' ); ?>

						<?php do_action( 'bp_after_group_body' ); ?>
					</div><!-- #item-body -->

					<?php do_action( 'bp_after_group_plugin_template' ); ?>

					<?php endwhile; endif; ?>

				</div><!-- .padder -->
			</div><!-- #content -->
		</div>
	</div>
	</div>			
</section>	
<?php get_footer( 'wplms_modern' ); ?>