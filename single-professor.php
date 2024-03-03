<?php get_header(); 
// single.php es la pagina que define el estilo de un enlace de post
?>
    <?php
    while (have_posts() ){
        the_post(); 
        pageBanner();
        
        ?>
        
    
    <div class="container container--narrow page-section">
        <div class="generic-content"> 
            <div class="row group">
                <div class="one-third"> 
                    <?php the_post_thumbnail('professorLandscape');?>  
                </div>
                <div class="two-thirds">
                    <?php
                        $likeCount = new WP_Query(array(
                            'post_type' => 'like',
                            'meta_query' => array(
                                array(
                                    'key' => 'liked_professor_id',
                                    'compare'=> '=',
                                    'value' => get_the_ID()
                                )
                            )
                        ));

                        $existStatus = 'no';

                        if(is_user_logged_in()){
                            $existQuery = new WP_Query(array(
                                'author' => get_current_user_id(),
                                'post_type' => 'like',
                                'meta_query' => array(
                                    array(
                                        'key' => 'liked_professor_id',
                                        'compare'=> '=',
                                        'value' => get_the_ID()
                                    )
                                )
                            ));
    
                            if($existQuery->found_posts){
                                $existStatus = 'yes';
                            }
                        }
                    ?>

                    <span 
                        class="like-box" 
                        data-exists="<?php echo $existStatus; ?>"
                        data-professor="<?php echo the_id(); ?>"
                        data-like="<?php if (isset($existQuery->posts[0]->ID)) echo $existQuery->posts[0]->ID; ?>"   
                    >
                        <i class="fa fa-heart-o" aria-hidden="true"> </i>
                        <i class="fa fa-heart" aria-hidden="true"> </i>
                        <span class="like-count"> <?php echo $likeCount->found_posts; ?> </span>
                    </span> 

                    <?php the_content() ?>  

                </div>
            </div> 
            
            

            <?php
                // La funcion get_field nos la da el plugin ACF y devuelve el valor del campo personalizado que hemos creado, en este caso, related-programs
                $relatedPrograms = get_field('related_programs');
                
                if($relatedPrograms){
                    echo '<hr class="section-break">';
                    echo '<h4 class="headline headline--medium"> Subjects Taught </h4>';
                    echo '<ul class="link-list min-list ">';
                    foreach($relatedPrograms as $program){ ?>
                        <li><a href="<?php echo get_the_permalink($program); ?>"> <?php echo get_the_title($program); ?> </a></li>
                    <?php }
                    echo '</ul>';
                }
            ?>

        </div>
    </div>
    <?php }


    get_footer();
?> 