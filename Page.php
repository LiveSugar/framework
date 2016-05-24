<?php
namespace livesugar\framework;

class Page {

  private static $apps;
  private static $view;

  public function __construct(){
    self::$apps = new Apps();
    self::$view = new View();
    $file = Path::$page.'/'.Path::$path.'/index.phtml';
    if(is_file($file)) {
      require $file;
    } else {
      http_response_code(404);
    }
  }

}
?>
