<?php

namespace bone;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bone
 *
 * @author cashaw
 */
class Settings{
  
  public static $db = null; 
  public static $dev_group = ''; 
  public static $faux_class = 'null';
  public static $layout_view = '\layout\default\sixteen_wide';
  public static $wrapper_view = 'index';
      
  public static function init(Array $values){
    foreach($values as $property => $value){  
      if(isset(self::$$property)){         
       self::$$property = $value;
      }     
    }    
  }
      
}
    