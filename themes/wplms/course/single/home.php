<?php 
/**
 * The template for displaying Course home
 *
 * Override this template by copying it to yourtheme/course/single/home.php
 *
 * @author 		VibeThemes
 * @package 	vibe-course-module/templates
 * @version     2.0
 */

do_action('wplms_before_single_course');

get_header( vibe_get_header() ); 

$course_layout = vibe_get_customizer('course_layout');
if ( bp_course_has_items() ) : while ( bp_course_has_items() ) : bp_course_the_item();

vibe_include_template("course/top$course_layout.php");  
?>

<?php do_action( 'template_notices' ); ?>
<div id="item-body">
	
	<?php 
	
	do_action( 'bp_before_course_body' );

	/**
	 * Does this next bit look familiar? If not, go check out WordPress's
	 * /wp-includes/template-loader.php file.
	 *
	 * @todo A real template hierarchy? Gasp!
	 */
	$current_action = bp_current_action();
	
	if(!empty($_GET['action'])){
		$current_action = $_GET['action'];
	}

	// Visual composer comaptibility
    if ( class_exists("Vc_base") ) {
      $vc_base = new Vc_base();
      $vc_base->createShortCodes();
    }
    // End VC Compatibility
	global $bp;
	if(!empty($current_action)):

		switch($current_action){
			case 'curriculum':
				locate_template( array( 'course/single/curriculum.php'  ), true );
			break;
			case 'members':
				locate_template( array( 'course/single/members.php'  ), true );
			break;
			case 'activity':
				locate_template( array( 'course/single/activity.php'  ), true );
			break;
			case 'submissions':
			case 'stats':
			case 'admin':
				$uid = bp_loggedin_user_id();
				$authors=array($post->post_author);
				$authors = apply_filters('wplms_course_instructors',$authors,$post->ID);
				
				if(current_user_can( 'manage_options' ) || in_array($uid,$authors)){
					locate_template( array( 'course/single/admin.php'  ), true );	
				}else{
					vibe_include_template("course/front$course_layout.php",'course/single/front.php');
				}
			break;
			case 'questions':
			case 'news':
			case 'events':
			break;
			case 'home':
			default:
				if ( isset($_POST['review_course']) && isset($_POST['review']) && wp_verify_nonce($_POST['review'],get_the_ID()) ){
				 	global $withcomments;
		    	  	$withcomments = true;
			      	comments_template('/course-review.php',true);
				}else if(isset($_POST['submit_course']) && isset($_POST['review']) && wp_verify_nonce($_POST['review'],get_the_ID())){ // Only for Validation purpose
					bp_course_check_course_complete();
				}else{
					if(function_exists('bp_course_is_plugin') && bp_course_is_plugin()){
						locate_template( array( 'course/single/plugins.php'), true );
					}else{
						vibe_include_template("course/front$course_layout.php",'course/single/front.php');
					}
				}
			break;
		}
		do_action('wplms_load_templates');
	else :
		if ( isset($_POST['review_course']) && isset($_POST['review']) && wp_verify_nonce($_POST['review'],get_the_ID()) ){
		 	global $withcomments;
    	  	$withcomments = true;
	      	comments_template('/course-review.php',true);
		}else if(isset($_POST['submit_course']) && isset($_POST['review']) && wp_verify_nonce($_POST['review'],get_the_ID())){ // Only for Validation purpose
			bp_course_check_course_complete();
		}else{
			vibe_include_template("course/front$course_layout.php",'course/single/front.php');
		}

	endif;
		
	do_action( 'bp_after_course_body' ); ?>

</div><!-- #item-body -->

<?php do_action( 'bp_after_course_home_content' ); 

vibe_include_template("course/bottom$course_layout.php");  

endwhile; endif; 
?>
<?php get_footer( vibe_get_footer() );  