<?php

require get_theme_file_path( '/includes/search-route.php' );
require get_theme_file_path( '/includes/like-route.php' );

function unversity_custom_rest(){
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() {return get_the_author();}
    ));

    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function() {return count_user_posts(get_current_user_id(), 'note');}
    ));
}

add_action('rest_api_init','unversity_custom_rest');

//Functio del page banner
function pageBanner($args = NULL){

    // $args = NULL significa que el que passem per paràmetre es opcional

    if (!isset($args['title'])) {
        $args['title'] = get_the_title();
    }
    if (!isset($args['subtitle'])) {
        $args['subtitle'] = get_field('page_subtitle');
    }
    if (!isset($args['photo'])) {
        if (get_field('page_banner_background_image') AND !is_archive() AND !is_home()) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
    ?>
    <div class="page-banner">
            <div class="page-banner__bg-image" 
            style="background-image: url(<?php echo $args['photo']; ?>)">
        
            </div>
                
                <div class="page-banner__content container container--narrow">
                    <h1 class="page-banner__title"> <?php echo $args['title']; ?></h1>
                    <div class="page-banner__intro">
                        <p>
                            <?php echo $args['subtitle']; ?>
                        </p>
                    </div>
                </div>
        </div>
<?php 
}

// add_action es una funcion que se ejecuta y añade una funcion para que se ejecute, en este caso es ejecutar funcion files
function university_files(){
    // Linea de codigo que permite usar JS en el documento usando JQuery
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'),array('jquery'), '1.0', true);

    wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('custom-google-fonts','//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('university_main_style', get_theme_file_uri('build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('build/index.css'));

    // Creamos esta funcion para poder usarla en JS y obtener el resultado de la funcion de WP get_site_url()
    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));

    // Esta funcion crea una variable que se llama universityData = {'root_url':'http:\/\/fictional-university.dev'}
    //Es el dominio base del WP para que sea dinamico, usamos en search.js  la variable asi universityData.root_url
}
add_action('wp_enqueue_scripts','university_files');

// Esta funcion es para que se muestre en el navegador el nombre de la pagina
function university_features(){
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerLocation1', 'Footer Location 1');
    register_nav_menu('footerLocation2', 'Footer Location 2');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape',400,260,true); // define esta dimension y se llama "professorLandscape"
    add_image_size('professorPortrait',480, 650,true); // true significa crop=true
    add_image_size('professorPortrait',480, 650,array('left','top'));
    //left y top significa per on es retallara la imatge. By default, ho fa pel centre si es true
    add_image_size('pageBanner',1500, 400,true);
    // esta funcion hace que podamos usar otras resoluciones de las imagenes que WP automaticamente genera para que se adapte en cualquier formato y de las imagenes futuro que  pongamos 
}
add_action('after_setup_theme','university_features');


function university_adjust_queries($query){
    if(!is_admin() AND is_post_type_archive('program') AND is_main_query()){
        $query-> set('orderby','title');
        $query-> set('order','ASC');
        $query-> set('posts_per_page',-1);
    }


    if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query(  )){
        $today = date('Ymd');
        $query->set('meta_key','event_date');
        $query->set('orderby','meta_value_num');
        $query->set('order','ASC');
        $query->set('meta_query',array(
            'key' => 'event_date',
            'compare' => '>=',
            'value' => $today,
            'type' => 'numeric'
        ));
    }    
}

add_action('pre_get_posts','university_adjust_queries');

// Redirect subscriber accounts out of admin and onto homepage
add_action('admin_init', 'redirectSubsToFrontend');
function redirectSubsToFrontend(){
    // Funcion que al loguear lleva a la pagina principal
    $ourCurrentUser = wp_get_current_user();
    
    if(count($ourCurrentUser -> roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber' ){
        wp_redirect(site_url('/'));
        exit; 
    }
}

add_action('wp_loaded', 'noSubsAdminBar');
function noSubsAdminBar(){
    
    $ourCurrentUser = wp_get_current_user();
    
    if(count($ourCurrentUser -> roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber' ){
        show_admin_bar(false);
    }
}

// Customize Login Screen
add_filter('login_headerurl','ourHeaderUrl');

function ourHeaderUrl(){
    return esc_url(site_url('/'));
}

// Customize Login Screen
add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS(){

    wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('custom-google-fonts','//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('university_main_style', get_theme_file_uri('build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('build/index.css'));

}

add_filter('login_headertitle', 'ourLoginTitle');

function ourLoginTitle(){
    return get_bloginfo('name');
}



// Force Note Posts to be private
add_filter('wp_insert_post_data', 'makeNotePrivate', 10,2);
// Abans: add_filter('wp_insert_post_data', 'makeNotePrivate', 1,2);
// 2 significa 2 parámetres , 10 es la prioridad


function makeNotePrivate($data, $postarr){
    
    if($data['post_type'] == 'note' ){
        if(count_user_posts(get_current_user_id(), 'note') > 5 AND !$postarr['ID'])
        die("You have reached your note limit");
    }

    // $data es toda la informacion del post_type
    if($data['post_type'] == 'note' AND $data['post_status'] != 'trash'){
        $data['post_status']= 'private';
    }

    // Funcion para limpiar todo lo que el usuario introduce en el text area
    if($data['post_type'] == 'note'){
        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }
    
    return $data;
}

// Funcion para evitar que la carpeta node_modules se incluya en la immigracion de web
add_filter('aiwm_exclude_content_from_export', 'ignoreCertainFiles');

function ignoreCertainFiles($exclude_filters){
    $exclude_filters[] = 'themes/rawnatheme/node_modules';
    return $exclude_filters;
}

