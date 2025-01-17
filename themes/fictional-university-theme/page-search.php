<?php get_header();

while(have_posts()){
    the_post();
    pageBanner();    
?>

<div class="container container--narrow page-section">

<?php
$parent_id = wp_get_post_parent_id(get_the_ID());
if($parent_id  !=0){ ?>
<div class="metabox metabox--position-up metabox--with-home-link">
  <p>
    <a class="metabox__blog-home-link" href="<? echo get_permalink($parent_id )?>">
        <i class="fa fa-home" aria-hidden="true"></i>
        Back to <?php echo get_the_title($parent_id ); ?>
    </a>
    <span class="metabox__main"><?php the_title(); ?></span>
  </p>
</div>
<?php }
 ?>


<?php
    $childPages = get_pages(array(
        'child_of'=> get_the_ID()
    ));

//show menu only on parent and children pages 
if($parent_id !=0 || $childPages){ ?>
<div class="page-links">
  <h2 class="page-links__title">
    <a href="<?php echo get_the_permalink($parent_id)?>">
  <?php echo get_the_title($parent_id)?>
</a>
</h2>
  <ul class="min-list">
    <?php 
       $findChildrenOf = $parent_id ==0 ? get_the_ID() : $parent_id;

       //automatically displays <li><a>the stuff</a></li>
        wp_list_pages(array(
            'title_li'=> null,
            'child_of'=> $findChildrenOf,
            'sort_column'=> 'menu_order'
        ));
    ?>
  </ul>
</div>
<?php } ?>
    <div class="generic-content"><?php 
    //get the html from pagesearch.php
    get_search_form(); ?></div>
</div>

<?php
}

get_footer();