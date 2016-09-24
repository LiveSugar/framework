<?php
namespace livesugar\framework;

class Path {

  public static $dir;
  public Static $page;
  public Static $libs;
  public Static $view;
  public Static $js;
  public Static $css;
  public Static $path;
  public Static $referer;

  public function __construct($dir){
    self::$page = $dir.'/page/';
    self::$libs = $dir.'/lib/';
    self::$view = $dir.'/view/';
    self::$js = $dir.'/js/';
    self::$css = $dir.'/css/';
    $path = function($path){
      $path = parse_url($path)['path'];
      $path = urldecode($path);
      $path = explode('/',$path);
      foreach($path as $key=>$value) { $value = trim($value); if(empty($value)) { unset($path[$key]); } else { $path[$key] = $value; } }
      $path = array_values($path);
      if(empty($path)) $path = ['index'];
      $path = implode('/',$path);
      return $path;
    };
    if(isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])){
      self::$path = $path('//'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI']);
      $referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '' ;
      self::$referer = $path($referer);
    }
  }

}
?>
