
<section id="content">
	<div id="buddypress">
		<div class="<?php echo vibe_get_container(); ?>">
			<div class="row">
				<div class="col-md-9">
					<?php 
					/**
					 * Fires before the display of member home content.
					 *
					 * @since BuddyPress (1.2.0)
					 */
					do_action( 'bp_before_group_home_content' ); ?>
					<div id="item-header" role="complementary">
						<?php locate_template( array( 'groups/single/group-header3.php' ), true );?>
					</div><!-- #item-header -->

					<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
						<ul>

							<?php bp_get_options_nav(); ?>

							<?php do_action( 'bp_group_options_nav' ); ?>

						</ul>
					</div>
