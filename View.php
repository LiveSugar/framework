<?php
namespace livesugar\framework;

class View {
  
  private static $register = [];
  private static $apps;
  private static $info;

  public function __construct(){
    self::$apps = new Apps;
    self::$info = new Info;
  }

  public function __get($name){
    self::$register['path'][] = $name;
    return new self;
  }

  public function __call($name,$value){
    if(isset(self::$register['path'])) $path = self::$register['path'];
    else $path = [];
    $path[] = $name;
    self::$register['path'] = [];
    $path = implode('/',$path);
    $file = Path::$view.'/'.$path.'/index.phtml';
    if(is_file($file)) require $file;
  }

}
?>
