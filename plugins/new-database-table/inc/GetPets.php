<?php

class GetPets{
    function __construct(){
        global $wpdb;
        $tablename = $wpdb->prefix . 'pets';

        $this->args = $this->getArgs();

        //$this->placeholders = $this->createPlaceholders();

        //has to be double quotes around the query
        $query = "SELECT * FROM $tablename ";
        $query .= $this->createWhereText();
        $query .= " LIMIT 100";

        $countQuery = "SELECT COUNT(*) FROM $tablename ";
        $countQuery .= $this->createWhereText();

        $this->count = $wpdb->get_var($wpdb->prepare($countQuery, $this->args));
        $this->pets = $wpdb->get_results($wpdb->prepare($query, $this->args));
    }

    function getArgs() {
        $temp = [];
     
        if (isset($_GET['favcolor'])) $temp['favcolor'] = sanitize_text_field($_GET['favcolor']);
        if (isset($_GET['species'])) $temp['species'] = sanitize_text_field($_GET['species']);
        if (isset($_GET['minyear'])) $temp['minyear'] = sanitize_text_field($_GET['minyear']);
        if (isset($_GET['maxyear'])) $temp['maxyear'] = sanitize_text_field($_GET['maxyear']);
        if (isset($_GET['minweight'])) $temp['minweight'] = sanitize_text_field($_GET['minweight']);
        if (isset($_GET['maxweight'])) $temp['maxweight'] = sanitize_text_field($_GET['maxweight']);
        if (isset($_GET['favhobby'])) $temp['favhobby'] = sanitize_text_field($_GET['favhobby']);
        if (isset($_GET['favfood'])) $temp['favfood'] = sanitize_text_field($_GET['favfood']);
     
        //whis is the purpose of this?
        return array_filter($temp, function($x){
            return $x;
        });
     
      }

      function createWhereText(){
        if(count($this->args) == 0) return "";

        $whereQuery = "WHERE ";

        $currentPosition = 0;
        foreach($this->args as $attribute => $value){
            $whereQuery .= $this->specificQuery($attribute);
            if($currentPosition < count($this->args) -1){
                $whereQuery .= " AND ";
            }
            $currentPosition++;
        }

        return $whereQuery;
        
      }

      function specificQuery($attribute){
        //break is not necessary we are returning out of function
        switch($attribute){
            case 'minweight': return "petweight >= %d"; break;
            case 'maxweight': return "petweight <= %d"; break;
            case 'minyear': return "birthyear >= %d"; break;
            case 'maxyear': return "birthyear <= %d"; break;
            default: return $attribute . "=%s";
        }
      }

}


