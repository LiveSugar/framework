<?php
namespace livesugar\framework;

class File {

  public static $file = [];

  public function add($file){
    self::$file[] = $file;
  }

  public function get(){
    return self::$file;
  }


}
?>
