<?php 
    get_header(); 
    pageBanner(array(
        'title' => 'All programs',
        'subtitle' => 'The programs of our university'
    ));    
?>

<div class="container container--narrow page-section">
    
    <ul class="link-list min-list"> 
    <?php
        while(have_posts()){
            the_post(); // Este comando nos da todos los datos del post/pagina ?> 
                <li><a href="<?php the_permalink();?>"> <?php the_title();?> </a></li>

        <?php }
            echo paginate_links();

    ?>
    
        </ul>
    <hr class="section-break"> 
    
</div> 

    
<?php get_footer(); ?>