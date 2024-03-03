<?php 
    get_header(); 
    pageBanner(array(
        'title' => 'Search Results',
        'subtitle' => "You searched for &ldquo;" . esc_html(get_search_query(false)) . "&rdquo;"

    ));    
?>

<div class="container container--narrow page-section">
    <?php
        if(have_posts()){
            while(have_posts()){
                the_post(); // Este comando nos da todos los datos del post/pagina 
                // Con esta funcion, haremos que en funcion del post_type, cogera un template u otro, por ejemplo, si post_type = campus,,, el template sera template-parts/content-campus... el - lo incorpora WP no lo tenemos que reescribir
                get_template_part('template-parts/content', get_post_type()); 
                }
                echo paginate_links();
        }
        else{
            echo '<h2 class="headline headline--small"> No results match that search.</h2>';
        }

        get_search_form();
        

?>
</div> 

    
<?php get_footer(); ?>