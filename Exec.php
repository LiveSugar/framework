<?php
namespace livesugar\framework;

class Exec {

  public $result;

  public function __construct($method,$json){
    $data = json_decode($json,true);
    $file = Path::$libs.'/'.$method.'.php';
    if(!is_file($file)) return false;
    $function = require($file);
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
    $api = (isset(Apps::$register['api'])) ? Apps::$register['api'] : [] ;
    if(!isset($api[$method])) $this->result = null;
    else $this->result = $apps;
  }

  public function json(){
    return json_encode($this->result);
  }

}
?>
