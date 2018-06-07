<?php
return function(){
  static $public = true;
  $gen = function(){
    $hash = microtime(true);
    $hash = number_format($hash,6,'.','');
    $hash = str_replace('.','',$hash);
    $hash = random_int(1,9).$hash;
    //$hash = str_split($hash);
    //$hash = $hash[0].$hash[13].$hash[1].$hash[12].$hash[2].$hash[11].$hash[3].$hash[10].$hash[4].$hash[9].$hash[5].$hash[8].$hash[6].$hash[7];
    $hash = gmp_strval($hash, 36);
    return $hash;
  };
  /*
  $arr = [];
  $len = [];
  for($i=0; $i<=1; $i++){
    $hash = $gen();
    $arr[] = $hash;
    $len[] = strlen($hash);
  }
  $p1 = count($arr);
  $p2 = count(array_unique($arr));
  var_dump($p1);
  var_dump($p2);
  echo "\r\n".number_format((100-($p2/$p1*100)),2,'.','')."%\r\n";
  var_dump(array_unique($len));

  return;
  $hash = random_int(10000,99999).$hash;
  $hash = str_replace('.','',$hash);
  $hash = gmp_strval($hash, 62);
  $len = mb_strlen($hash,'UTF-8');
  if($len !== 14) $hash = $this->uid();
   */
  return $gen();
}
?>
