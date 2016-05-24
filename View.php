<?php
namespace livesugar\framework;

class View {
  
  private static $register = [];
  private static $dir;
  private static $apps;

  public function __construct($dir=null,$dirApps){
    self::$apps = new Apps($dirApps);
    if(is_dir($dir)) self::$dir = $dir;
  }

  public function __get($name){
    self::$register['path'][] = $name;
    return new self;
  }

  public function __call($name,$value){
    $path = self::$register['path'];
    $path[] = $name;
    self::$register['path'] = [];
    $path = implode('/',$path);
    $file = self::$dir.'/'.$path.'/index.phtml';
    if(is_file($file)) require $file;
  }

}
?>
