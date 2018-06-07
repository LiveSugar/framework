<?php
return function($execute,$sql){
  $db = $this->db->pg();
  $sql = $db->prepare($sql);
  $sql->execute($execute);
  $res = $sql->fetchAll();
  $err = $sql->errorInfo();
  if(is_null($err[2])) return $res;
  else return $err;
}
?>
