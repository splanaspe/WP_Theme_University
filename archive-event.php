<?php 
    get_header(); 
    pageBanner(array(
        'title' => 'All Events',
        'subtitle' => "See what's going on our world"
    ));
?>

<div class="container container--narrow page-section">
    <?php

        while(have_posts()){
            the_post(); // Este comando nos da todos los datos del post/pagina 
            get_template_part('template-parts/content','event');
            ?> 
            
        <?php }
            echo paginate_links();

    ?>
    
    <hr class="section-break"> 
    <p> Looking for a recap of past events? <a href="<?php echo site_url('/past-events'); ?>"> ¡Click Here!</a></p>
</div> 

    
<?php get_footer(); ?>