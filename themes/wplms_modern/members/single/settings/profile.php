<?php

/**
 * BuddyPress Member Settings
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
							<?php do_action( 'bp_before_member_body' ); ?>
							<div class="item-list-tabs no-ajax" id="subnav">
								<ul>

									<?php bp_get_options_nav(); ?>

									<?php do_action( 'bp_member_plugin_options_nav' ); ?>

								</ul>
							</div><!-- .item-list-tabs -->
							<form action="<?php echo trailingslashit( bp_displayed_user_domain() . bp_get_settings_slug() . '/profile' ); ?>" method="post" class="standard-form" id="settings-form">

								<?php if ( bp_xprofile_get_settings_fields() ) : ?>

									<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

										<?php if ( bp_profile_fields() ) : ?>

											<table class="profile-settings" id="xprofile-settings-<?php bp_the_profile_group_slug(); ?>">
												<thead>
													<tr>
														<th class="title field-group-name"><?php bp_the_profile_group_name(); ?></th>
														<th class="title"><?php _e( 'Visibility', 'wplms_modern' ); ?></th>
													</tr>
												</thead>

												<tbody>

													<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

														<tr <?php bp_field_css_class(); ?>>
															<td class="field-name"><?php bp_the_profile_field_name(); ?></td>
															<td class="field-visibility"><?php bp_profile_settings_visibility_select(); ?></td>
														</tr>

													<?php endwhile; ?>

												</tbody>
											</table>

										<?php endif; ?>

									<?php endwhile; ?>

								<?php endif; ?>

								<?php do_action( 'bp_core_xprofile_settings_before_submit' ); ?>

								<div class="submit">
									<input id="submit" type="submit" name="xprofile-settings-submit" value="<?php esc_attr_e( 'Save Settings', 'wplms_modern' ); ?>" class="auto" />
								</div>

								<?php do_action( 'bp_core_xprofile_settings_after_submit' ); ?>

								<?php wp_nonce_field( 'bp_xprofile_settings' ); ?>

								<input type="hidden" name="field_ids" id="field_ids" value="<?php bp_the_profile_group_field_ids(); ?>" />

							</form>
						<?php do_action( 'bp_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_settings_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->
	</div><!-- row -->
	</div><!-- buddypress -->
</section>	
<?php get_footer( 'wplms_modern' ); ?>
