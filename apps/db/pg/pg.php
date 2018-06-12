<?php
return function($host,$base,$user,$pass) {
  static $singleton = true;
  $db = new PDO('pgsql:host='.$host.';dbname='.$base.'',$user,$pass,[PDO::ATTR_PERSISTENT => true]);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  return $db;
};
?>
