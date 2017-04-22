<?php
namespace livesugar\framework;

class Join {
  public static $address = '';

  public function gate($url){
    $this->url = $url; 
    return $this;
  }

  public function exec($api,$data){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt ($ch, CURLOPT_HTTPHEADER, ["Accept: api://".$api,"Content-Type: application/json"]); 
    $result = curl_exec($ch); 
    return $result;
  }

}

?>
