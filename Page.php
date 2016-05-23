<?php
namespace humanitysoft\humanity;

class Page {

  private static $apps;
  private static $view;

  public function __construct($filePage,$dirLibs,$dirView){
    self::$apps = new Apps($dirLibs);
    self::$view = new View($dirView);
    if(is_file($filePage)) require $filePage;
  }

}
?>
