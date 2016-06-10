<?php
namespace livesugar\framework;

class Apis {

  public function __invoke($domain,$name,$data) {
    $data = json_encode($data,JSON_UNESCAPED_UNICODE);
    $url = 'http://'.$domain;
    var_dump($url);
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

}

?>
