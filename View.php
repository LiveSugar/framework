<?php
namespace livesugar\framework;

class View {
  
  private static $register = ['path'=>[]];
  private static $apps;
  private static $info;
  private static $meta;
  private static $join;

  public function __construct(){
    self::$apps = new Apps;
    self::$info = new Info;
    self::$meta = new Meta;
    self::$join = new Join;
  }

  public function __get($name){
    self::$register['path'][] = $name;
    return new self;
  }

  public function __call($name,$value){
    array_push(self::$register['path'],$name);
    $path = implode('/',self::$register['path']);
    self::$register['path'] = [];
    $file = Path::$view.'/'.$path.'/index.phtml';
    if(is_file($file)) require $file;
    self::$info->view($path);
  }

}
?>
