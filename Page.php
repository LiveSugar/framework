<?php
namespace livesugar\framework;

class Page {

  private static $apps;
  private static $view;
  private static $info;
  private static $meta;
  private static $join;

  public function __construct($load=true){
    self::$apps = new Apps;
    self::$view = new View;
    self::$info = new Info;
    self::$meta = new Meta;
    self::$join = new Join;
    $file = Path::$page.'/'.Path::$path.'/index.phtml';
    if(is_file($file)) {
      require $file;
    } else {
      http_response_code(404);
    }
  }

}
?>
