<?php
return function() {
  static $singleton = true;
  $conf = ['host'=>'host','base'=>'base','user'=>'user','pass'=>'***'];
  $db = new PDO('pgsql:host='.$conf['host'].';dbname='.$conf['base'].'',$conf['user'],$conf['pass'],[PDO::ATTR_PERSISTENT => true]);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  return $db;
};
?>
