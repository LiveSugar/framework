<?php
namespace livesugar\framework;

class Save {

  private static $file = '/tmp/livesugar';


  public static function set($data){
    $data = json_encode($data);
    if(is_file(self::$file)) touch(self::$file);
    file_put_contents(self::$file,$data);
    return true;
  }
  public static function get(){
    if(!is_file(self::$file)) return [];
    $data = file_get_contents(self::$file);
    $data = json_decode($data,true);
    if(empty($data) || !is_array($data)) $data = [];
    return $data;
  }

}
?>
