<?php

/**
 * BuddyPress Delete Account
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

				<?php do_action( 'bp_before_member_body' ); ?>

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>

						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_member_plugin_options_nav' ); ?>

					</ul>
				</div><!-- .item-list-tabs -->

				<h3><?php _e( 'Delete Account', 'wplms_modern' ); ?></h3>

				<div id="message" class="info">
					
					<?php if ( bp_is_my_profile() ) : ?>

						<p><?php _e( 'Deleting your account will delete all of the content you have created. It will be completely irrecoverable.', 'wplms_modern' ); ?></p>
						
					<?php else : ?>

						<p><?php _e( 'Deleting this account will delete all of the content it has created. It will be completely irrecoverable.', 'wplms_modern' ); ?></p>

					<?php endif; ?>

				</div>

				<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/delete-account'; ?>" name="account-delete-form" id="account-delete-form" class="standard-form" method="post">

					<?php do_action( 'bp_members_delete_account_before_submit' ); ?>

					<label>
						<input type="checkbox" name="delete-account-understand" id="delete-account-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-account-button').disabled = ''; } else { document.getElementById('delete-account-button').disabled = 'disabled'; }" />
						 <?php _e( 'I understand the consequences.', 'wplms_modern' ); ?>
					</label>

					<div class="submit">
						<input type="submit" disabled="disabled" value="<?php _e( 'Delete Account', 'wplms_modern' ); ?>" id="delete-account-button" name="delete-account-button" />
					</div>

					<?php do_action( 'bp_members_delete_account_after_submit' ); ?>

					<?php wp_nonce_field( 'delete-account' ); ?>

				</form>

				<?php do_action( 'bp_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_settings_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->
	<div class="col-md-3 col-sm-3">
		<?php get_sidebar( 'buddypress' ); ?>
	</div>
	</div><!-- row -->
	</div><!-- buddypress -->
</section>	

<?php get_footer( 'wplms_modern' ); ?>