<?php
namespace humanitysoft\humanity;

class App {

  private static $libs;
  private static $view;

  public function __construct($filePage,$dirLibs,$dirView){
    self::$libs = new Libs($dirLibs);
    self::$view = new View($dirView);
    if(is_file($filePage)) require $filePage;
  }

}
?>
