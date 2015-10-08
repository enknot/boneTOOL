<?php

namespace bone;

use \App as App;


class Variable{    
  
    /**
     * a prefix for the table name incase you want to use the name variable for 
     * something else.  Set this up in your definitions file like so:
     * Variable::$pre='my_prefix';     * 
     * @var string
     * @disabled
     */
    private static $tpre = '';
    

    
    private static $sql = array(
        'get' => "SELECT * FROM `variable` WHERE `name` = :name;",
        'set' => "REPLACE INTO `variable` (`name`, `value`) VALUES (:name, :value);",
        'del' => "DELETE FROM `variable` WHERE `name` = :name;", 
        'dump' => "SELECT * FROM `variable`;",
        'create' => "CREATE TABLE IF NOT EXISTS variable (
                        `name` varchar(128) NOT NULL DEFAULT '' 
                        COMMENT 'The name of the variable.',
                        `value` longblob    NOT NULL 
                        COMMENT 'The value of the variable.',
                    PRIMARY KEY (`name`));"
    );
    
    /**
     *  Gets the value of the variable named $name or returns the value of 
     *  $default, which is NULL by default.
     * @param type $name
     * @param type $default
     * @example      * 
     * $fella = Variable::get('app_dude', 'man'); 
     * Sets $fella to the stored value for 'app_dude', or 'man' if there's 
     * no value there.
     */
    public static function get($name, $default = NULL){
                
        self::checkConnection();
                
        $prep = \bone\Settings::$db->prepare(self::$sql['get']);
        $prep->execute(array(':name' => $name));
        $rows = $prep->fetchAll();        
        
        if($rows){
            return unserialize($rows[0]->value);
        }  
         unset($prep);
        return $default;        
    }
    
    private static function checkConnection(){
         if(is_null(\bone\Settings::$db)){
            throw new \Exception("Attempt to use \bone\Variable without database 
            connection. Assign a connection at \bone\Settings::\$db");   
            die;
        }
    }
    
    /**
     * Sets the value of a variable and pops it in to the database
     * @param type $name
     * @example path description
     * Variable::set('app_dude', 'man');      * 
     * Sets the variable named 'app_dude' to t$value for 'app_dude', or 'man' if there's 
     * no value there.
     */
    public static function set($name, $value){
        
        self::checkConnection();
        
       $prep = \bone\Settings::$db->prepare(self::$sql['set']);   
       $svalue = serialize($value);
       $prep->execute(array(':name' => $name, ':value' => $svalue));
       unset($prep);
    }    
    
    /**
     * Removes the variable from the system
     * @param type $name
     */
    public static function del($name){
        
        self::checkConnection();
        
        $prep = \bone\Settings::$db->prepare(self::$sql['del']);
        $prep->execute(array(':name' => $name));
        unset($prep);
    }
    
    /**
     * grabs an array of stored variables
     * @param array $exclude
     */
    public static function dump(array $exclude = array()){
        
        self::checkConnection();
        
         $rows = \bone\Settings::$db->query(self::$sql['dump']);
         $ret = array();
         
         foreach($rows as $row){        
             if(!in_array($row->name, $exclude))
             $ret[$row->name] = unserialize($row->value);
         }
         
         return $ret;
    }
    
    /**
     * Installs the variable table to your database
     */
    public static function install(){       
        self::checkConnection();
      \bone\Settings::$db->exec(self::$sql['create']);
    }
    
    /**
     * Add if statements to the SQL clause to be sure you're not over writting 
     * and existing table.
     */
    public static function table(){
        
      self::checkConnection();
      
      $sql = "
        CREATE TABLE IF NOT EXISTS `variable` (
          `name` varchar(128) NOT NULL DEFAULT '' COMMENT 'The name of the variable.',
          `value` longblob NOT NULL COMMENT 'The value of the variable.',
          PRIMARY KEY (`name`)
        );";
      \bone\Settings::$db->query($sql);
    }
    
    /**
     * Adds all the variables requested to the data array if they are set and
     * 
     */
    public static function addToData(&$data_array, $var_names = null){
        $ret = false;
        
        if(is_string($var_names)){
            $val = self::set($var_names, FALSE);
            if($val){
                $ret = TRUE;
                $other_array = array($var_names, $val);
            }
        }else{
            
            $all = self::dump();
            if(is_array($var_names)){
                foreach($var_names as $name => $value){
                    if(!in_array($name, $all)){
                        unset($all[$name]);
                    }
                }
            }else{
                $other_array = $all;
            }
        }
        
        array_merge($data_array, self::dump());
    }

        
    
}