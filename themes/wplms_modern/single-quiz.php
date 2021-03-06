<?php


do_action('wplms_before_quiz');
get_header('wplms_modern');
$user_id = get_current_user_id();
do_action('wplms_before_quiz_begining',get_the_ID());
$quiztaken=get_user_meta($user_id,get_the_ID(),true);
if ( have_posts() ) : while ( have_posts() ) : the_post();

?>
<section class="title-area">
<?php if(has_post_thumbnail()){ 
        $url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' ); 
    }else{
        $vibe = Wplms_Modern_Init::init();
        $url = $vibe->option('hero_img');
    }
?>
    <div class="title-content" style="background:url(<?php echo (is_Array($url)?$url[0]:$url); ?>) no-repeat 50% 50%; ">
        <div class="container">
            <div class="title-text">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="pagetitle">
                            
                            <h1><?php the_title(); ?></h1>
                            <?php the_sub_title(); ?>

                        </div>
                    </div>
                </div>    
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <div class="quiz_next">
                            <?php
                                if(is_user_logged_in()){
                                    if(isset($quiztaken) && $quiztaken){
                                        if($quiztaken > time()){
                                            echo apply_filters('wplms_continue_quiz_button','<a class="button create-group-button full begin_quiz" data-quiz="'.get_the_ID().'"> '.__('Continue Quiz','wplms_modern').'</a>',get_the_ID());
                                                wp_nonce_field('start_quiz','start_quiz');
                                        }else{ 

                                            $quiz_unfinished_check=get_post_meta(get_the_ID(),$user_id,true);
                                            if(!isset($quiz_unfinished_check) || $quiz_unfinished_check ==''){
                                                add_post_meta(get_the_ID(),$user_id,0);
                                            }
                                            
                                            $quiz_course = get_post_meta(get_the_ID(),'vibe_quiz_course',true);

                                            if(isset($quiz_course) && is_numeric($quiz_course) && $quiz_course && wplms_user_course_active_check($user_id,$quiz_course)){
                                                echo '<a href="'.bp_loggedin_user_domain().BP_COURSE_SLUG.'/'.BP_COURSE_RESULTS_SLUG.'/?action='.get_the_ID().'" class="button full"> '.__('Check Quiz Results','wplms_modern').'</a>';
                                                $take_course_page=get_permalink(vibe_get_option('take_course_page'));
                                                echo '<form action="'.$take_course_page.'" method="post">';
                                                    echo '<input type="submit" class="button full" value="'.__('Back to Course','wplms_modern').'">';
                                                    wp_nonce_field('continue_course'.$user_id,'continue_course'); 
                                                    echo  '<input type="hidden" name="course_id" value="'.$quiz_course.'" />';
                                                echo  '</form>'; 
                                                //echo '<a href="'.get_permalink($quiz_course).'" class="button full"> '.__('Back to Course','wplms_modern').'</a>';
                                            }else{
                                                echo '<a href="'.bp_loggedin_user_domain().'course/course-results/?action='.get_the_ID().'" class="button create-group-button full"> '.__('Check Quiz Results','wplms_modern').'</a>';
                                            }
                                        }
                                    }else{
                                        echo apply_filters('wplms_start_quiz_button','<a class="button create-group-button full begin_quiz" data-quiz="'.get_the_ID().'"> '.__('Start Quiz','wplms_modern').'</a>',get_the_ID());
                                         wp_nonce_field('start_quiz','start_quiz');
                                    }
                                }else{
                                    echo '<a class="button create-group-button full"> '.__('Take a Course to Start the Quiz','wplms_modern').'</a>';
                                         
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="content">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="content">
                    <?php
                        the_quiz();
                        do_action('wplms_front_end_quiz_controls');
                    ?>
                </div>
            </div>
            <div class="col-md-3 quiz-sidebar">
                <div class="quiz_details">
                 <?php
                    the_quiz_timer();
                    the_quiz_timeline();
                ?>
                </div>
                <?php
                do_action('wplms_front_end_quiz_meta_controls');
                ?>
            </div>
             <?php
                endwhile;
                endif;
                ?>
        </div>
    </div>
</section>
<?php
get_footer('wplms_modern');
