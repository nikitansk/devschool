<?php

/**
 * BuddyPress Member Settings
 *
 * @package BuddyPress
 * @subpackage bp-default
 */


get_header( vibe_get_header() ); 

$profile_layout = vibe_get_customizer('profile_layout');

vibe_include_template("profile/top$profile_layout.php");  
?>
<div id="item-body">
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
							<th class="title"><?php _e( 'Visibility', 'vibe' ); ?></th>
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
		<input id="submit" type="submit" name="xprofile-settings-submit" value="<?php esc_attr_e( 'Save Settings', 'vibe' ); ?>" class="auto" />
	</div>

	<?php do_action( 'bp_core_xprofile_settings_after_submit' ); ?>

	<?php wp_nonce_field( 'bp_xprofile_settings' ); ?>

	<input type="hidden" name="field_ids" id="field_ids" value="<?php bp_the_profile_group_field_ids(); ?>" />

	</form>
	<?php do_action( 'bp_after_member_body' ); ?>

</div><!-- #item-body -->

<?php do_action( 'bp_after_member_settings_template' ); ?>

<?php

vibe_include_template("profile/bottom.php");  

get_footer( vibe_get_footer() );  