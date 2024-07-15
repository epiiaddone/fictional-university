<?php

require get_theme_file_path('/inc/search-route.php');
require get_theme_file_path('/inc/like-route.php');

//add more data to rest api
function university_custom_rest(){
    register_rest_field(
        'post',//post type to customize
        'authorName',//name of new field
        array(
            'get_callback'=> function(){ return get_the_author();}
        )
    );

    register_rest_field(
        'note',//post type to customize
        'userNoteCount',//name of new field
        array(
            'get_callback'=> function(){ return count_user_posts(get_current_user_id(), 'note');}
        )
    );
}
add_action('rest_api_init', 'university_custom_rest');

function pageBanner($args = NULL){ 
    
    $title = isset($args['title']) ? $args['title'] : get_the_title();
    $subtitle = isset($args['subtitle']) ? $args['subtitle'] : get_field('page_banner_subtitle');

    if(isset($args['photo'])){
        $photo = $args['photo'];
    }else{        
        if(get_field('page_banner_background_image') AND !is_archive() AND !is_home()){
            $photo = get_field('page_banner_background_image')['sizes']['pageBanner'];
        }else{
            $photo = get_theme_file_uri('/images/ocean.jpg');
        }
    }
    ?>
    <div class="page-banner">
    <div class="page-banner__bg-image"
        style="background-image: url(<?php echo $photo ?>)">
    </div>
    <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php echo $title; ?></h1>
        <div class="page-banner__intro">
            <p><?php echo $subtitle; ?></p>
        </div>
    </div>
</div>
<?php
}


function university_files(){
    wp_enqueue_script('main-script', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0.0', true);
    wp_enqueue_style('google-font', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

    //variables we want to access on the front end
    wp_add_inline_script(
    'main-script',//needs to match a script that was enqueued
    'const MYSCRIPT = ' . json_encode( array(
        'site_url' => get_site_url(),
        'nonce'=>wp_create_nonce('wp_rest')//unique to user session
    ) ), 'before' );
}
add_action('wp_enqueue_scripts', 'university_files');


function university_features(){
    //automatic broswer tab titles
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    //wp automatically creates multiple images when upload an image
    //but it can only make smaller images than the uploaded image
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);

    //set up menus that is editable from the admin page
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerLocationOne', 'Footer Location One');
    register_nav_menu('footerLocationTwo', 'Footer Location Two');

    //for block theme
    add_theme_support('editor-styles');
    add_editor_style(array(
        'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i',
        'build/style-index.css',
        'build/index.css'
    ));
}
add_action('after_setup_theme', 'university_features');


function university_adjust_query($query){

    $today = date('Ymd');    
    //don't alter custom queries
    //only alter for event archive page
    if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()){
        $query->set('posts_per_page', 10);
        $query->set('order_by','meta_value_num');
        $query->set('meta_key','event_date');
        $query->set('order','ASC');
        $query->set('meta_query', array(
            array(
              'key'=>'event_date',
              'compare'=>'>=',
              'value'=> $today,
              'type'=>'numeric'
            )
            ));
    }    

    if(!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()){
        $query->set('posts_per_page', -1);
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
    } 
}
add_action('pre_get_posts', 'university_adjust_query');


function redirectSubsToFrontend(){
    $ourCurrentUser = wp_get_current_user();
    if(count($ourCurrentUser->roles)==1 AND $ourCurrentUser->roles[0]=='subscriber'){
        wp_redirect(site_url('/'));
        exit;
    }
}
add_action('admin_init', 'redirectSubsToFrontend');


function noSubsAdminBar(){
    $ourCurrentUser = wp_get_current_user();
    if(count($ourCurrentUser->roles)==1 AND $ourCurrentUser->roles[0]=='subscriber'){
        show_admin_bar(false);
    }
}
add_action('wp_loaded', 'noSubsAdminBar');

//customize login screen
function customLoginHeaderUrl(){
    return esc_url(site_url('/'));
}
add_filter('login_headerurl', 'customLoginHeaderUrl');


//these are the same css assets that are loaded in an above method
function customLoginCSS(){
wp_enqueue_style('google-font', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}
add_action('login_enqueue_scripts', 'customLoginCSS');


function ourLoginTitle(){
    return get_bloginfo('name');
}
add_filter('login_headertitle', 'ourLoginTitle');



function filterNotes($data, $postarr){
    define("MAX_NOTES_PER_USER", 5);

    //limit amount of notes per user
    if($data['post_type'] == 'note'){
        if(!array_intersect( ['administrator'], wp_get_current_user()->roles) 
        AND count_user_posts(get_current_user_id(), 'note') >= MAX_NOTES_PER_USER
        AND !$postarr['ID']
        ){
            die("You have reached you note limit");
        }
    }

    //remove html from notes
    if($data['post_type'] == 'note'){
        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }

    //force notes to be private
    if($data['post_type']=='note' AND $data['post_status'] != 'trash'){
        $data['post_status'] = 'private';
    }
    return $data;
}
add_filter('wp_insert_post_data',
            'filterNotes',
            10, //priority of this filter for this hook
            2 //number of arguments filterNotes will recieve
        );


class JSXBlock{
    function __construct($name){
        $this->name = $name;
        add_action('init', [$this, 'onInit']);
    }

    function onInit(){
        wp_register_script($this->name,
            get_stylesheet_directory_uri() . "/build/{$this->name}.js",
            array('wp-blocks', 'wp-editor'
        ));

        register_block_type("ourblocktheme/{$this->name}", array(
            'editor_script' => $this->name
        ));
    }
}

//need to create an instance for each block type
new JSXBlock('genericheading');
new JSXBlock('banner');
new JSXBlock('genericbutton');