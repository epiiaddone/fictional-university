<?php

get_header();
pageBanner(array(
  'title'=> 'Upcoming Events',
  'subtitle'=>'See what is going on'
));
?>

<div class="container container--narrow page-section">
        <?php
        while(have_posts()){
            the_post();?>
  <div class="event-summary">
<a class="event-summary__date t-center" href="<? the_permalink(); ?>">
                <span class="event-summary__month"><?php
                $date =  new DateTime(get_field('event_date'));
               echo $date->format('M'); ?>
                </span>
                <span class="event-summary__day"><?php echo $date->format('d'); ?></span>
              </a>
              <div class="event-summary__content">
                <h5 class="event-summary__title headline headline--tiny">
                  <a href="<? the_permalink(); ?>"><?php the_title(); ?></a>
                </h5>
                <p>
                  <?php echo wp_trim_words(get_the_content(), 18); ?>
                  <a href="<? the_permalink(); ?>" class="nu gray">Learn more</a>
                </p>
              </div>
            </div>
            
<?php  } 
        echo paginate_links();
    ?>

<hr class="section-break">
    <p>Looking for past events? 
      <a href="<?php echo site_url('/past-events'); ?>">Check out our past events</a>
        </p>
</div>

<?php
get_footer();

