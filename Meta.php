<?php
namespace livesugar\framework;

class Meta {

  public static $title = [];
  public static $description;
  public static $keyword = [];

  public function title($title){
    self::$title[] = $title;
  }

  public function description($description){
    self::$description = $description;
  }

  public function keyword($keyword){
    self::$keyword[] = $keyword;
  }

}
?>
