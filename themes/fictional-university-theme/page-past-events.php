<?php
get_header();
pageBanner(array(
  'title'=>'All Past Events',
  'subtitle'=>'See what happened'
));
?>

<div class="container container--narrow page-section">
        <?php
          $today = date('Ymd');
          wp_reset_postdata();
          $pastEvents = new WP_Query(array(
            'paged'=>get_query_var('paged', 1),
            'posts_per_page'=>8,
            'post_type'=>'event',
            'order_by'=>'meta_value_num',
            'meta_key'=>'event_date',
            'order'=>'ASC',
            'meta_query'=>array(
              array(
                'key'=>'event_date',
                'compare'=>'<',
                'value'=> $today,
                'type'=>'numeric'
              )
            )
          ));
        while($pastEvents->have_posts()){
            $pastEvents->the_post();
          get_template_part('template-parts/content-event');
            
  } 
        
        echo paginate_links(array(
            'total'=>$pastEvents->max_num_pages
        ));
    ?>
</div>

<?php
get_footer();

