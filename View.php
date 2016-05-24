<?php
namespace livesugar\framework;

class View {
  
  private static $register = [];
  private static $apps;

  public function __construct(){
    self::$apps = new Apps();
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
    $file = Path::$view.'/'.$path.'/index.phtml';
    if(is_file($file)) require $file;
  }

}
?>
