<?php
return function($db,$execute,$sql){
  $sql = $db->prepare($sql);
  $sql->execute($execute);
  $res = $sql->fetchAll();
  if(isset($res[0])) return $res;
  else return $sql->errorInfo();
}
?>
