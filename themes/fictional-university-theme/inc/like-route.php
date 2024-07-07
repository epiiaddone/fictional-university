<?php



function universityLikeRoutes(){
    //for POST method
    register_rest_route(
        'university/v1',//namespace
        'manageLike', 
        array(
            'methods'=> 'POST',
            'callback'=> 'createLike'
        )
        );

        // for DELETE method
        register_rest_route(
            'university/v1',//namespace
            'manageLike', 
            array(
                'methods'=> 'DELETE',
                'callback'=> 'deleteLike'
            )
            );
}
add_action('rest_api_init', 'universityLikeRoutes');


function createLike($data){

    if(!is_user_logged_in()) die("Only logged in users can create a like");
    if(get_post_type($data['professorID']) != 'professor') die("Invalid professorID");

    $existQuery = new WP_Query(array(
            'author'=> get_current_user_id(),
            'post_type'=>'like',
            'meta_query'=> array(
                array(
                    'key'=>'liked_professor_id',
                    'compare'=>'=',
                    'value'=> $data['professorID'])
            )
        ));
        wp_reset_postdata();
    if($existQuery->found_posts) die("Only one like per professor per user allowed");

    $newLikeID = wp_insert_post(array(
        'post_type'=>'like',
        'post_status'=> 'publish',
        'post_title'=> 'user:' . get_current_user_id() . ' professor:' . sanitize_text_field($data['professorID']),
        'meta_input'=>array(
            'liked_professor_id'=>sanitize_text_field($data['professorID'])
        )
    ));
    //need to return something so the js knows there was success
  return [
        'success'=>1,
        'result'=> 'like created',
        'likeID'=> $newLikeID
  ];
}


function deleteLike($data){
    if(!is_user_logged_in()) die("Only logged in users can delete a like");

    $likeId = sanitize_text_field($data['like']);
    if(get_current_user_id() != get_post_field('post_author', $likeId)) die("Not authorized to delete like");
    if(get_post_type($likeId) != 'like') die("invalid delete like request");

    wp_delete_post($likeId, true);

    //need to return something so the js knows there was success
    return [
        "success"=> 1,
        "result"=>"like deleted"
        ];

}