<?php

/*
  Plugin Name: Pet Adoption (New DB Table)
  Version: 1.0
  Author: Brad
  Author URI: https://www.udemy.com/user/bradschiff/
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once plugin_dir_path(__FILE__) . 'inc/generatePet.php';

class PetAdoptionTablePlugin {
  function __construct() {

    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "pets";

    //run when plugin is activated
    add_action('activate_new-database-table/new-database-table.php', array($this, 'onActivate'));

    //run when admin page refreshed
    //add_action('admin_head', array($this, 'populateFast'));


    add_action('wp_enqueue_scripts', array($this, 'loadAssets'));

    //this hooks into the form and is run on form submit
    //<input type="hidden" name="action" value="createpet"/>
    add_action('admin_post_createpet', array($this, 'createPet'));

    //<input type="hidden" name="action" value="deletepet"/>
    add_action('admin_post_deletepet', array($this, 'deletePet'));

    //similar to content filter
    add_filter('template_include', array($this, 'loadTemplate'), 99);
  }

  function deletePet(){
    if(!isset($_POST['idtodelete'])){      
      wp_safe_redirect(site_url('/pet-adoption'));
      exit("invalid id to delete");
    }
    if(!current_user_can('administrator')){
      wp_safe_redirect(site_url);
      exit("only admin can delete");
    }
     
      $idToDelete = sanitize_text_field($_POST['idtodelete']);           

      global $wpdb;
      $deleteResult = $wpdb->delete($this->tablename, array('id'=>$idToDelete));

      if(!$deleteResult){
        echo nl2br("delete fail \n");
        echo nl2br("\n \n query: \n");
        print_r($wpdb->last_query);
        echo nl2br("\n \n error: \n");
        print_r($wpdb->last_error);        
      }else{
        wp_safe_redirect(site_url('/pet-adoption'));
        exit;
      }   
  }

  function createPet(){
     if(!isset($_POST['incomingpetname'])){      
      wp_safe_redirect(site_url('/pet-adoption'));
      exit;
    }
    if(!current_user_can('administrator')){
      wp_safe_redirect(site_url);
      exit;
    }
    
      $pet = generatePet();

      //$format = array("%d", "%d", "%s", "%s", "%s", "%s", "%s", "%s");

      //i believe this sanitize is unnecessary as done by wpdb::insert automatically
      $pet['petname'] = sanitize_text_field($_POST['incomingpetname']);           

      global $wpdb;
      $insertResult = $wpdb->insert($this->tablename, $pet);

      if(!$insertResult){
        echo nl2br("insert fail \n");
        echo nl2br("\n pet data: \n");
        print_r($pet);
        echo nl2br("\n \n query: \n");
        print_r($wpdb->last_query);
        echo nl2br("\n \n error: \n");
        print_r($wpdb->last_error);
        
      }else{
        wp_safe_redirect(site_url('/pet-adoption'));
        exit;
      }      
  }


  function onActivate() {
    //needed to create a table
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    //delta as in difference - this will not create the db table twice
    //the code below has to be very precise
    //e.g. need to have two spaces after PRIMAY KEY
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      birthyear smallint(5) NOT NULL DEFAULT 0,
      petweight smallint(5) NOT NULL DEFAULT 0,
      favfood varchar(60) NOT NULL DEFAULT '',
      favhobby varchar(60) NOT NULL DEFAULT '',
      favcolor varchar(60) NOT NULL DEFAULT '',
      petname varchar(60) NOT NULL DEFAULT '',
      species varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  function onAdminRefresh() {
    global $wpdb;
    $wpdb->insert($this->tablename, generatePet());
  }

  function loadAssets() {
    if (is_page('pet-adoption')) {
      wp_enqueue_style('petadoptioncss', plugin_dir_url(__FILE__) . 'pet-adoption.css');
    }
  }

  function loadTemplate($template) {
    if (is_page('pet-adoption')) {
      return plugin_dir_path(__FILE__) . 'inc/template-pets.php';
    }
    return $template;
  }

  function populateFast() {
    $query = "INSERT INTO $this->tablename (`species`, `birthyear`, `petweight`, `favfood`, `favhobby`, `favcolor`, `petname`) VALUES ";
    $numberofpets = 10000;
    for ($i = 0; $i < $numberofpets; $i++) {
      $pet = generatePet();
      $query .= "('{$pet['species']}', {$pet['birthyear']}, {$pet['petweight']}, '{$pet['favfood']}', '{$pet['favhobby']}', '{$pet['favcolor']}', '{$pet['petname']}')";
      if ($i != $numberofpets - 1) {
        $query .= ", ";
      }
    }
    /*
    Never use query directly like this without using $wpdb->prepare in the
    real world. I'm only using it this way here because the values I'm 
    inserting are coming fromy my innocent pet generator function so I
    know they are not malicious, and I simply want this example script
    to execute as quickly as possible and not use too much memory.
    */
    global $wpdb;
    $wpdb->query($query);
  }

}

$petAdoptionTablePlugin = new PetAdoptionTablePlugin();