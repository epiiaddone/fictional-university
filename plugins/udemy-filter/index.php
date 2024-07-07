<?php

/*
Plugin Name: Udemy filter plugin
Description: A simple filter
Version: 1.0.2
Author: Greg R
Author URI: https://www.github.com/epiiaddone
*/

//exit if used directly
if(!defined('ABSPATH')) exit;

class WordFilterPlugin{

    function __construct(){
        add_action('admin_menu', array($this, 'ourMenu'));
        add_action('admin_init', array($this, 'ourSettings'));
        add_filter('the_content', array($this, 'filterLogic'));
    }

    function ourSettings(){
        add_settings_section(
            'replacement-text-section',//section name
            null, //label text
            null, //description text
            'word-filter-options'//slug name of page to display this is
        );
        register_setting(
            'replacementFields',//group name
            'replacementText' //option name
        );
        add_settings_field(
            'replacement-text', //element id
            'Filtered Text', //user visible label
            array($this, 'replacementFieldHTML'), //html output function
            'word-filter-options', //slug of page this appears on
            'replacement-text-section' //name of section to attach this field
        );
    }

    function replacementFieldHTML(){ ?>
    <input
        type="text"
        name="replacementText"
        value="<?php echo esc_attr(get_option('replacementText', '****'),) ?>"
    >
    <p class="description">Leave blank to remove the filtered words without replacement.</p>
    <?php }

    function filterLogic($content){
        $blackListWords = explode(',', get_option('plugin_words_to_filter'));
        $blackListWordsTrimmed = array_map('trim', $blackListWords);
        return str_replace($blackListWordsTrimmed, esc_html(get_option('replacementText', '****')), $content);
    }

    function ourMenu(){
        $mainPageHook = add_menu_page(
           'Words To Filter', //document title
           'Word Filter', //title
           'manage_options', //permission level
           'ourwordfilter', //slug
           array($this, 'wordFilterPage'), //html output function
           plugin_dir_url(__FILE__) . 'zombie.svg',
           //'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAwIDEwMDAiPjxkZWZzPjxjbGlwUGF0aCBpZD0iYSI+PHBhdGggZD0iTTAgMzloMTAwMHY0MjFIMHoiIGNsYXNzPSJhIi8+PC9jbGlwUGF0aD48Y2xpcFBhdGggaWQ9ImIiPjxwYXRoIGQ9Ik0yNDAgNDIwaDIyMHY1MjBIMjQweiIgY2xhc3M9ImEiLz48L2NsaXBQYXRoPjxzdHlsZT4uYSwuYntmaWxsOm5vbmV9LmJ7c3Ryb2tlOiNmZmY7c3Ryb2tlLWxpbmVjYXA6c3F1YXJlO3N0cm9rZS1taXRlcmxpbWl0OjI7c3Ryb2tlLXdpZHRoOjIwcHh9PC9zdHlsZT48L2RlZnM+PHBhdGggZD0iTTI0MCA5NDBWNDIwaDIyMHY0NDBjMCA0MC00MCA4MC04MCA4ME0zNDAgMzAwaDMyMCIgY2xhc3M9ImIiLz48cGF0aCBkPSJNNzgwIDMwMEM2NzYgMjQ0LjkgNTgwIDE2OSA1MDAgNjBjLTgwIDEwOS0xNzYgMTg0LjktMjgwIDI0MCIgY2xhc3M9ImIiIHN0eWxlPSJjbGlwLXBhdGg6dXJsKCNhKSIvPjxwYXRoIGQ9Ik02MDAgNzgwVjQ2ME03NjAgNDIwdjQ0MGMwIDQwLTQwIDgwLTgwIDgwaC02MCIgY2xhc3M9ImIiLz48ZyBzdHlsZT0iY2xpcC1wYXRoOnVybCgjYikiPjxwYXRoIGQ9Ik00NjAgNzQwSDI0ME00NjAgNTgwSDI0MCIgY2xhc3M9ImIiLz48L2c+PC9zdmc+', //icon
           69 //position in menu
        );

        add_submenu_page(
            'ourwordfilter',//slug of menu page
            'Words to Filter',// display text
            'Words List', // menu title
            'manage_options', //permission level
            'ourwordfilter', //match slug of menu page to overwite default title
            array($this, 'wordFilterPage')//html function is same as menu page
        );

        add_submenu_page(
            'ourwordfilter',//slug of menu page
            'Word Filter Options',// display text
            'Options', //menu title
            'manage_options', //permission level
            'word-filter-options', //slug of this page
            array($this, 'optionsSubPage')//html function
        );

        add_action("load-{$mainPageHook}", array($this, 'mainPageAssets'));
    }

    function mainPageAssets(){
        wp_enqueue_style('filterAdminCSS', plugin_dir_url(__FILE__) . 'styles.css');
    }

    function handleForm(){
        //check that post request came from our site
        if(wp_verify_nonce($_POST['ourNonce'], 'saveFilterWords') AND current_user_can('manage_options')){
        update_option('plugin_words_to_filter', sanitize_text_field($_POST['plugin_words_to_filter'])); ?>
        <div class="updated">
            <p>Changes saved</p>
        </div>
    <?php }else{?>
<div class="error"><p>Sorry, you do not have permission to do that action.</p></div>
    <?php }
    }

    function wordFilterPage(){?>
    <div class="wrap">
        <h1>Word filter</h1>
        <?php if(isset($_POST['justsubmitted'])) $this->handleForm(); ?>
        <form method="POST">
            <input type="hidden" name="justsubmitted" value="true">
            <?php  
            //to ensure that the post request comes from our site
            wp_nonce_field('saveFilterWords', 'ourNonce');
             ?>
            <label for="plugin_words_to_filter"><p>Enter a <strong>comma-seperated</strong> list of words to filter from your site's content.</p></label>
        <div class="word-filter__flex-container">
            <textarea name="plugin_words_to_filter" id="plugin_words_to_filter" placeholder="butt head, poopie face"><?php 
            echo(esc_textarea(get_option('plugin_words_to_filter'))); ?></textarea>
        </div>
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"/>
        </form>
    </div>
    <?php }

    function optionsSubPage(){?>
    <div class="wrap">
        <h1>Word Filter Options</h1>
        <form action="options.php" method="POST">
            <?php
            settings_errors();//wp will automatically call this on a setting page
            settings_fields('replacementFields');
            do_settings_sections('word-filter-options');
            submit_button();
        ?>
        </form>
    </div>
    <?php }
}


//stored as a variable allows other plugins to use
$wordFilterPlugin = new WordFilterPlugin(); 