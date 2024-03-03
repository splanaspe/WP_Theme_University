<?php

add_action('rest_api_init', 'universityRegisterSearch');

function universityRegisterSearch(){
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'universitySearchResults'
    ));
    // WP_REST_SERVER::READABLE ==== GET
    // CALLBACK es lo que devuelve, definimos la funcion que esta debajo
}

function universitySearchResults($data){
    // $data es una variable que contiene todo lo que ha escrito el usario de más en el enlace, por ejemplo, si escribe http://test.local//wp-json/university/v1/search?term=life,,, $data['term'] = 'life'
    $mainQuery = new WP_Query(array(
        'post_type' => array(
            'post',
            'page',
            'professor',
            'campus',
            'program',
            'event'
        ),
        's' => sanitize_text_field($data['term'])
    ));

    // sanitize_text_field($data['term']) esta funcion de WP es para comprobar que lo que ha escribido el usuario no es ningun codigo malicioso para inyeccion sql

    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

    while($mainQuery->have_posts()){
        $mainQuery->the_post(); // Este comando nos da todo los datos del professor

        if(get_post_type() == 'post' OR get_post_type()== 'page'){
            array_push($results['generalInfo'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author( )
            ));
        }

        if(get_post_type() == 'professor'){
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0,'professorPortrait')
            ));
        }

        if(get_post_type() == 'program'){
            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id'=> get_the_ID()
            ));
        }

        if(get_post_type() == 'campus'){
            array_push($results['campuses'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
            ));
        }

        if(get_post_type() == 'event'){
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if(has_excerpt()){
                $description = get_the_excerpt();
            }else{
                $description = wp_trim_words(get_the_content(),18);
            }

            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
        }
    }


    if($results['programs']){
        $programsMetaQuery = array('relation'=> 'OR');

    foreach($results['programs'] as $item){
        array_push( $programsMetaQuery , 
            array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] .'"'
            )
        );
    }


    $programRelationShip = new WP_Query(array(
        'post_type' => array('professor' , 'event'),
        'meta_query' => $programsMetaQuery
    ));

    while($programRelationShip->have_posts()){
        $programRelationShip->the_post();

        if(get_post_type() == 'professor'){
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0,'professorPortrait')
            ));
        }

        if(get_post_type() == 'event'){
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if(has_excerpt()){
                $description = get_the_excerpt();
            }else{
                $description = wp_trim_words(get_the_content(),18);
            }

            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
        }

    }
    
    // Esta funcion de PHP elimina las duplicidades dentro del array y array_values renombra los elementos del array para enumerarlos correctamente
    $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
    $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    
    }

    
    return $results;
}

?>