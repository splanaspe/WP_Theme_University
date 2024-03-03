<?php 

    get_header(); 
    
// single.php es la pagina que define el estilo de un enlace de post
?>
    <?php
    while (have_posts() ){
        the_post(); 
        pageBanner();
        
        ?>
        
    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('event');?>"><i class="fa fa-home"   aria-hidden="true"></i> 
                    Events 
                </a> 
                <span class="metabox__main">
                    <?php the_title();?> 
                </span>
            </p>
        </div>

        <div class="generic-content"> 
            <?php the_content() ?>  
            
            <?php
                // La funcion get_field nos la da el plugin ACF y devuelve el valor del campo personalizado que hemos creado, en este caso, related-programs
                $relatedPrograms = get_field('related_programs');
                
                if($relatedPrograms){
                    echo '<hr class="section-break">';
                    echo '<h4 class="headline headline--medium"> Related Programs </h4>';
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