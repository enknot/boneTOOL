<?php
namespace bone\Controllers;

class BoneInclude extends Index{
  
  protected $bone_path;
  
  protected $content_type;
  
  public function __construct(){
    parent::__construct();
    $this->bone_path = ROOT . '/vendor/enknot/bonetool/include';
  }
  
}
?>
