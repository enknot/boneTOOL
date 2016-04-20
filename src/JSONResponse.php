<?php

namespace bone;

class JSONResponse {
  
  private $error = array();
  
  private $warning = array();
  
  private $success = array();
  
  private $callback = array(); 
  
  private $values = array();
  
  private static $valid_message = array('error', 'warning', 'success');
  
  public function message($message, $type = 'error'){    
    
//    if(!in_array($type, self::$valid_message))return;
    
    switch($type){
      case 'error':
          $this->error[] = $message;
        break;
      case 'warning':
        $this->warning[] = $message;
        break;
      case 'success':
        $this->success[] = $message;
        break;
    }
  }
  
  
  public function value($name, $value){
    $this->values[$name] = $value;
  }
  
  public function callback($function_name,Array $params = array()){
    $this->callback[$function_name][] = $params;    
  }
  
  public function export(){

    $ret = array();
    
    if(count($this->values) > 0 )$ret = $this->values;    
    
    //messages
    foreach(self::$valid_message as $type){
      if(count($this->$type) > 0)$ret['messages'][$type] = $this->$type;
    }

    //callbacks
   if(count($this->callback) > 0)$ret['callbacks'] = $this->callback;
   
   return json_encode($ret);
    
  }
  
  
}
