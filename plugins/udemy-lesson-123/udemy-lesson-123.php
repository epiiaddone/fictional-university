<?php

/*
Plugin Name: Udemy Lesson 123 plugin
Description: A simple plugin to test the basics
Version: 1.0.2
Author: Greg R
Author URI: https://www.github.com/epiiaddone
Text domain: wcpdomain
Domain Path: /languages
*/

class WordCountAndTimePlugin{
function __construct(){
    add_action('admin_menu', array($this, 'adminPage'));
    add_action('admin_init', array($this, 'settings'));
    add_filter('the_content', array($this, 'ifWrap'));
    add_action('init', array($this, 'languages'));
}

function languages(){
    load_plugin_textdomain(
        'wcpdomain',//text domain
        false, //depricated
        dirname(plugin_basename(__FILE__)) . '/languages' //path to languages folder
    );
}

// i guess the add_filter() passes in the $contect
function ifWrap($content){

    if( is_main_query()
    AND is_single()
    AND (get_option('wcp_wordcount', '1')
        OR get_option('wcp_charactercount', '1')
        OR get_option('wcp_readtime', '1')
        )){
        return $this->createHTML($content);
    }else{
        return $content;
    }
}

function createHTML($content){
    $html = '<h3>' . esc_html(get_option('wcp_headline', 'Post Statistics')) . '</h3><p>';

    $wordCount = str_word_count(strip_tags($content));

    if(get_option('wcp_wordcount', '1')){
        $html .= esc_html__('This post has', 'wcpdomain') . ' ' . $wordCount . ' ' . esc_html__('words', 'wcpdomain') . '.<br>';
    }

    if(get_option('wcp_charactercount', '1')){
        $html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>';
    }

    if(get_option('wcp_readtime', '1')){
        $html .= 'This post will take about ' . round($wordCount/225) . ' minutes to read.<br>';
    }

    $html .= '</p>';

    if(get_option('wcp_location', '1') == 0){
        return $html . $content;
    }

    return $content . $html;
}

function settings(){

    add_settings_section(
        'wcp_first_section',//section name
        null,//label text for users
        null, //helpful text for users
        'word-count-settings-page'//page slug
    );

    add_settings_field(
        'wcp_location', //setting name
        'Display Location',//label text for users
        array($this, 'locationHTML'),
        'word-count-settings-page',//page slug
        'wcp_first_section'//section name
    );

    register_setting(
        'wordcountplugin',//group name
        'wcp_location', //setting name, this is saved under 'option_name' in db
        array(
            'sanitize_callback' => array($this,'sanitizeLocation'),//wp provided function
            'defalt' => '0'//note int stored as string
        )
    );

    
    add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'word-count-settings-page', 'wcp_first_section');
    register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default'=> 'Post Statistics'));

    add_settings_field('wcp_wordcount', 'Word Count', array($this, 'wordCountHTML'), 'word-count-settings-page', 'wcp_first_section');
    register_setting('wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default'=> '1'));

    add_settings_field('wcp_charactercount', 'Character Count', array($this, 'characterCountHTML'), 'word-count-settings-page', 'wcp_first_section');
    register_setting('wordcountplugin', 'wcp_charactercount', array('sanitize_callback' => 'sanitize_text_field', 'default'=> '1'));

    add_settings_field('wcp_readtime', 'Read Time', array($this, 'readTimeHTML'), 'word-count-settings-page', 'wcp_first_section');
    register_setting('wordcountplugin', 'wcp_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default'=> '1'));

}

function locationHTML(){ ?>
<select name="wcp_location">
    <option value="0" <?php selected(get_option('wcp_location'), 0)?>> Beginning of post </option>
    <option value="1" <?php selected(get_option('wcp_location'), 1)?>> End of post </option>
</select>
<?php
}

function headlineHTML(){?>
<input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline'))?>">
<?php }

function wordCountHTML(){?>
<input type="checkbox" name="wcp_wordcount" value="1" <?php checked(get_option('wcp_wordcount', '1'))?>>
<?php }

function characterCountHTML(){?>
<input type="checkbox" name="wcp_charactercount" value="1" <?php checked(get_option('wcp_charactercount', '1'))?>>
<?php }

function readTimeHTML(){?>
    <input type="checkbox" name="wcp_readtime" value="1" <?php checked(get_option('wcp_readtime', '1'))?>>
    <?php }

function sanitizeLocation($input){
    if($input!='1' AND $input !='0'){
        add_settings_error(
            'wcp_location',//which setting to apply to
            'wcp_location_error',// the id you want to give to the error
            'Display location must be either beginning or end'//message to user
        );
        //return current value
        return get_option('wcp_location');
    }
}

function adminPage(){
add_options_page(
    'Word Count Settings',//page title
    __('Word Count', 'wcpdomain'), //menu label
    'manage_options', //permission
    'word-count-settings-page', //unique slug
    array($this, 'ourSettingsPageHTML'), //function name
);
}

function ourSettingsPageHTML(){ ?>
    <div class="wrap">
        <h1>Word Count Settings</h1>
        <form action="options.php" method="POST">
            <?php
            settings_fields('wordcountplugin');
            do_settings_sections('word-count-settings-page');
            submit_button();
            ?>
        </form>
</div>
<?php }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();
?>