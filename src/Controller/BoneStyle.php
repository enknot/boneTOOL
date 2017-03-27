<?php
namespace Controllers;

class BoneStyle extends BoneInclude{
  
  protected $content_type = 'text/css';
  
  private function contentType(){
    $this->sendHeader("Content-type:");
    
  }
  
  /**
   * @servable true
   */
  public function index(){
    $this->contentType();
    return file_get_contents($this->bone_path . '/include/css/base.css');
  }
  
  /**
   * @servable true
   */
  public function bootstrap(){
    $this->contentType();
    return file_get_contents($this->bone_path . '/bootstrap/dist/css/bootstrap.min.css');
  }
  
}
