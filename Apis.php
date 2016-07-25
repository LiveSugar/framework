<?php
namespace livesugar\framework;

class Apis {

  private $name = false;
  private $file = false;
  private $data = false;

  public function __invoke($domain,$name,$data) {
    $data = json_encode($data,JSON_UNESCAPED_UNICODE);
    $url = 'http://'.$domain;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/octet-stream',
      'Accept: api://'.$name
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    $result = json_decode($result,true);
    return $result;
  }

  public function name($name){
    $this->name = $name;
    return $this;
  }
  
  public function file($file){
    $this->file = $file;
    return $this;
  }

  public function data($data){
    $this->data = $data;
    return $this;
  }

  public function exec(){
    if($this->file !== false){
      $url = parse_url($this->name);
      $name = preg_replace('/^\//','',$url['path']);
      $url = 'http://'.$url['host'];
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/octet-stream',
        'Accept: api[file]://'.$name
      ));
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $this->file);
      $result = curl_exec($ch);
      $result = json_decode($result,true);
      var_dump($result);
    }
    return $this;
  }

}

?>
