<?php


/*
Plugin Name: Are You Paying Attention Quiz
Description: Giver your users a multiple choice question.
Version: 1.0
Author: Brad
Author URI: https://github.com/epiiaddone
*/

if(! defined('ABSPATH')) exit;

class AreYouPayingAttention{
    function __construct(){
        add_action('init', array($this, 'adminAssets'));
    }

    function adminAssets(){
    register_block_type(
        __DIR__,
        array(
            'render_callback' => array($this, 'theHTML')
        )
        );
    }

    function theHTML($attributes){
        if(!is_admin()){
        //only load script if block is present on page
        /* block.json is responsible for loading these files
        wp_enqueue_script(
            'attentionFrontend',
            plugin_dir_url(__FILE__) . 'build/frontend.js',
            array('wp-element'),
            '1.0.0',
            array('strategy'  => 'defer')
        );
        wp_enqueue_style(
            'attentionFrontendStyle',
            plugin_dir_url(__FILE__) . 'build/frontend.css',
        );
        }
        */
    }

        ob_start(); ?>
        <div class="paying-attention-update-me">
            <pre style="display:none"><?php echo wp_json_encode($attributes); ?></pre>
        </div>

       <?php  return ob_get_clean();
    }
}

$areYouPayingAttention = new AreYouPayingAttention();