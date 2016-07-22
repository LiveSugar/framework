<?php
namespace livesugar\framework;

class Exec {

  public $result;

  public function __construct($method,$json=false){
    if($json !== false && !empty($json)) $data = json_decode($json,true);
    $file = Path::$libs.'/'.$method.'.php';
    if(!is_file($file)) return false;
    $function = require($file);
    if(is_array($function)) {
      $opts = $function[1];
      $opts = explode('|',$opts);
      $opts = array_flip($opts);
      $function = $function[0];
    }
    if(!is_callable($function)) return [];
    $reflection = new \ReflectionFunction($function);
    $params = [];
    foreach($reflection->getParameters() as $key=>$value){
      if(isset($data[$value->name])) $params[] = $data[$value->name];
      else $params[] = null;
    }
    $apps = new Apps;
    $while = explode('/',$method);
    $count = count($while);
    for($i=$count;$i > 0;$i--){
      $name = array_shift($while); 
      if($i == 1) $apps = call_user_func_array([$apps,$name],$params);
      else $apps = $apps->{$name};
    }
    if(isset($opts['PUBLIC'])) $this->result = $apps;
    else $this->result = null;
  }

  public function json(){
    return json_encode($this->result,JSON_UNESCAPED_UNICODE);
  }

}
?>
