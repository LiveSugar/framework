<?php
namespace livesugar\framework;

class Meta {

  public static $title = [];
  public static $description;
  public static $keywords = [];
  public static $viewport;

  public function title($title){
    self::$title[] = $title;
  }

  public function description($description){
    self::$description = $description;
  }

  public function keyword($keyword){
    self::$keywords[] = $keyword;
  }

  public function viewport($on=false){
    self::$viewport = $on;
  }

}
?>
