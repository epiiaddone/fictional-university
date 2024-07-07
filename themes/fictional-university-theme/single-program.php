<?php 
get_header();
pageBanner();

while(have_posts()){
    the_post();?>
    <!--content div -->
    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<? echo get_post_type_archive_link('program');?>">
                    <i class="fa fa-home" aria-hidden="true"></i>
                   All Programs
                </a>
                <span class="metabox__main"><?php the_title(); ?></span>
            </p>
        </div>
        <div class="generic-content"><?php echo get_field('main_body_content'); ?></div>
        <?php
///////////////////professors/////////////////////////
$relatedProfessors = new WP_Query(array(
    'posts_per_page'=>-1,
    'post_type'=>'professor',
    'order_by'=>'title',
    'order'=>'ASC',
    'meta_query'=>array(
    array(
        'key'=>'related_programs',
        'compare'=>'LIKE',
        'value'=> '"' . get_the_id() . '"',
    )
    )
));
if($relatedProfessors){
?>
<hr class="section-break"/>
<h2 class="headline headline--medium"><?php echo get_the_title();?> Professors</h2>
<ul class="professor-cards">
    <?php
    while($relatedProfessors->have_posts()){
        $relatedProfessors->the_post(); ?>
        <li class="professor-card__list-item">
            <a class="professor-card" href="<?php the_permalink(); ?>">
                <img class="professor-card__image" src="<?php the_post_thumbnail_url(); ?>">
                <span class="professor-card__name"><?php the_title(); ?></span>
            </a>
        </li>
    <?php } ?>
</ul>
<?php        
}

/////////////////////events//////////////////////////////////
            $today = date('Ymd');
            wp_reset_postdata();
            $relatedEvents = new WP_Query(array(
                'posts_per_page'=>-1,
                'post_type'=>'event',
                'order_by'=>'meta_value_num',
                'meta_key'=>'event_date',
                'order'=>'DSC',
                'meta_query'=>array(
                array(
                    'key'=>'event_date',
                    'compare'=>'>=',
                    'value'=> $today,
                    'type'=>'numeric'
                ),
                array(
                    'key'=>'related_programs',
                    'compare'=>'LIKE',
                    'value'=> '"' . get_the_id() . '"',
                )
                )
            ));
            if($relatedEvents){
            ?>
            <hr class="section-break"/>
            <h2 class="headline headline--medium">Upcoming <?php echo get_the_title();?> Events</h2>
            
            <?php
            while($relatedEvents->have_posts()){
                $relatedEvents->the_post(); 
                get_template_part('template-parts/content-event');
             }             
            }
            wp_reset_postdata();
        ?>
        </div><!--content div -->
  <?php
}
get_footer();