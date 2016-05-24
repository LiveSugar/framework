<?php
namespace livesugar\framework;

class Page {

  private static $apps;
  private static $view;

  public function __construct($filePage,$dirApps,$dirView){
    self::$apps = new Apps($dirLibs);
    self::$view = new View($dirView,$dirApps);
    if(is_file($filePage)) require $filePage;
  }

}
?>
