<?php 
get_header();
 pageBanner(); 

while(have_posts()){
    the_post();?>

    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<? echo get_post_type_archive_link('event');?>">
                    <i class="fa fa-home" aria-hidden="true"></i>
                   Events Home
                </a>
                <span class="metabox__main"><?php the_title(); ?></span>
            </p>
        </div>

        <div class="generic-content"><?php the_content(); ?></div>

        <?php 
            $relatedPrograms = get_field('related_programs');
            if($relatedPrograms){
            ?>
            <hr class="section-break"/>
            <h2 class="headline headline--medium">Related Programs</h2>
            <ul class="link-list min-list">
                <?php
                foreach($relatedPrograms as $program){ ?>
                    <li><a href="<? echo get_the_permalink($program); ?>">
                    <?php echo get_the_title($program); ?></a>
                </li>
            <?php }
            }
        ?>
        </ul>
        </div>
  <?php

}
get_footer();