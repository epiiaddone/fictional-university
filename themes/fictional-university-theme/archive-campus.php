<?php

get_header();
?>

<div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/ocean.jpg')?>)"></div>
        <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title">All Campuses</h1>
        <div class="page-banner__intro">
            <p>It's a crapshoot</p>
        </div>
    </div>
</div>

<div class="container container--narrow page-section">
    <div class="acf-map">
        <?php
        while(have_posts()){
            the_post();
            $mapLocation = get_field('map_location');
            ?>
    <div
        class="marker"
        data-lat="<?php $mapLocation['lat'] ?>"
        data-lng="<?php echo $mapLocation['long']; ?>"
    >
    </div>          
<?php  } 

    ?>
</div>

</div>

<?php
get_footer();

