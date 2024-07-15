<?php

function universityRegisterSearch(){
    register_rest_route(
        'university/v1',//name space
        'search',//route
        array(
            'methods'=> WP_REST_SERVER::READABLE,
            'callback'=> 'universitySearchResults'
        )
    );
}
add_action('rest_api_init', 'universityRegisterSearch');


function universitySearchResults($data){

    wp_reset_query();
    $query = new WP_Query(array(
        'post_type'=> array('post', 'page', 'professor', 'program', 'event', 'campus'),
        'posts_per_page'=>-1,
        's'=> sanitize_text_field($data['term'])
    ));

    $results = array(
        'generalInfo'=>array(),
        'professors'=>array(),
        'programs'=>array(),
        'events'=>array(),
        'campuses'=>array()
    );

    while($query->have_posts()){
        $query->the_post();
        if(get_post_type() == 'post' OR get_post_type() == 'page'){
            array_push($results['generalInfo'], array(
                'title'=>get_the_title(),
                'permalink'=>get_the_permalink(),
                'postType'=>get_post_type(),
                'authorName'=>get_the_author()
            ));
        }
        if(get_post_type() == 'professor'){
            array_push($results['professors'], array(
                'title'=>get_the_title(),
                'permalink'=>get_the_permalink(),
                'image'=>get_the_post_thumbnail_url(0, 'professorLandscape')
            ));
        }
        if(get_post_type() == 'program'){
            array_push($results['programs'], array(
                'title'=>get_the_title(),
                'permalink'=>get_the_permalink(),
                'id'=>get_the_id()
            ));

            $relatedCampuses = get_field('related_campuses');
            if($relatedCampuses){
                foreach($relatedCampuses as $campus){
                    array_push($results['campuses'], array(
                        'title'=> get_the_title($campus),
                        'permalink'=> get_the_permalink($campus)
                    ));
                }
            }
        }
        if(get_post_type() == 'campus'){
            array_push($results['campuses'], array(
                'title'=>get_the_title(),
                'permalink'=>get_the_permalink()
            ));
        }
        if(get_post_type() == 'event'){
            $date =  new DateTime(get_field('event_date'));

           $description = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 18);

            array_push($results['events'], array(
                'title'=>get_the_title(),
                'permalink'=>get_the_permalink(),
                'month'=> $date->format('M'),
                'day'=>$date->format('d'),
                'description'=>$description
            ));
        }

    }

    if($results['programs']){

        $programRelationshipQueryMeta = array('relationship' => 'OR');
        foreach($results['programs'] as $item){
            array_push($programRelationshipQueryMeta, array(
                'key'=> 'related_programs',
                'compare'=> 'LIKE',
                'value'=> '"' . $item['id'] . '"'
            ));
        }
            
        wp_reset_query();
        $programRelationshipQuery = new WP_Query(array(
            'post_type'=> array('professor','event'),
            'meta_query'=> $programRelationshipQueryMeta
            ));
    
            while($programRelationshipQuery->have_posts()){
                $programRelationshipQuery->the_post();

                if(get_post_type() == 'event'){
                    $date =  new DateTime(get_field('event_date'));
        
                   $description = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 18);
        
                    array_push($results['events'], array(
                        'title'=>get_the_title(),
                        'permalink'=>get_the_permalink(),
                        'month'=> $date->format('M'),
                        'day'=>$date->format('d'),
                        'description'=>$description
                    ));
                }

                if(get_post_type() == 'professor'){
                    array_push($results['professors'], array(
                        'title'=>get_the_title(),
                        'permalink'=>get_the_permalink(),
                        'image'=>get_the_post_thumbnail_url(0, 'professorLandscape')
                    ));
                }
            }            
    }
    $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
    $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));


    return $results;
}