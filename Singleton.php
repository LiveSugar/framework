<?php
namespace humanitysoft\humanity;

class Singleton {

  private static $register = [];

  public function set($name,$func){
    self::$register[$name] = $func;
  }

  public function get($name){
    if(isset(self::$register[$name])) return self::$register[$name];
    else return false;
  }

}
?>
