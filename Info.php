<?php
namespace livesugar\framework;

class Info {

  public $apps;
  public static $view = [];

  public function __construct(){

    $this->apps = [];
    $scandirs = function($path,$save=null) use (&$scandirs){
      $files = scandir($path);
      foreach($files as $file){
        if($file == '.' || $file == '..') { continue; }
        if(is_dir($path.'/'.$file)) {
          $scandirs($path.'/'.$file,$save.'/'.$file);
        } else {
          $appFile = $path.'/'.$file;
          if(!preg_match('/\.php$/Uui',$appFile)) continue;
          $appName = $save.'/'.$file;
          $appName = preg_replace('/\.php$/Uui','',$appName);
          $appName = preg_replace('/^\//Uui','',$appName);
          # Reflection
          $function = require($appFile);
          if(is_array($function)) {
            $opts = $function[1];
            $opts = explode('|',$opts);
            $opts = array_flip($opts);
            $function = $function[0];
          } else {
            unset($opts);
          }
          if(!is_callable($function)) return [];
          $reflection = new \ReflectionFunction($function);
          $params = [];
          foreach($reflection->getParameters() as $key=>$value){
            $params[] = $value->name;
          }
          $this->apps[$appName]['params'] = $params;
          $this->apps[$appName]['public'] = (isset($opts['PUBLIC'])) ? true : false ;
        }
      }
    };
    $scandirs(Path::$libs);

  }

  public function view($path){
    return array_push(self::$view,$path);
  }

}
?>
