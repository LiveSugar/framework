<?php
return function(){
  static $public = true;
  $hash = microtime(true);
  $hash = number_format($hash,6,'.','');
  $hash = str_replace('.','',$hash);
  $hash = random_int(1,9).$hash;
  $hash = gmp_strval($hash, 36);
  return $hash;
}
?>
