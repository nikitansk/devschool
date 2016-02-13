<?php

/**
 * BuddyPress Delete Account
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'wplms_modern' ); ?>

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

							<h3><?php _e( 'Capabilities', 'wplms_modern' ); ?></h3>

							<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/capabilities/'; ?>" name="account-capabilities-form" id="account-capabilities-form" class="standard-form" method="post">

								<?php do_action( 'bp_members_capabilities_account_before_submit' ); ?>

								<label>
									<input type="checkbox" name="user-spammer" id="user-spammer" value="1" <?php checked( bp_is_user_spammer( bp_displayed_user_id() ) ); ?> />
									 <?php _e( 'This user is a spammer.', 'wplms_modern' ); ?>
								</label>

								<div class="submit">
									<input type="submit" value="<?php _e( 'Save', 'wplms_modern' ); ?>" id="capabilities-submit" name="capabilities-submit" />
								</div>

								<?php do_action( 'bp_members_capabilities_account_after_submit' ); ?>

								<?php wp_nonce_field( 'capabilities' ); ?>

							</form>

							<?php do_action( 'bp_after_member_body' ); ?>

						</div><!-- #item-body -->

						<?php do_action( 'bp_after_member_settings_template' ); ?>

					</div><!-- .padder -->
				</div><!-- #content -->
			</div><!-- row -->
		</div><!-- container -->
	</div><!-- buddypress -->
</section>	
<?php get_footer( 'wplms_modern' ); ?>