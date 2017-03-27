<?php
namespace Controllers;

class BoneJS extends BoneInclude{
  
  /**
   * @servable true
   */
  public function index(){
    $this->sendHeader('Content-type: application/x-javascript');
    return file_get_contents($this->bone_path . '/js/app.js');
  }
  
  /**
   * @servable true
   */
  public function bootstrap(){
    $this->sendHeader('Content-type: application/x-javascript');
    return file_get_contents($this->bone_path . '/bootstrap/dist/js/bootstrap.min.js');
  }
  
  
  
}
