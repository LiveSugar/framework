<?php
namespace livesugar\framework;

class Apps {

    private static $apps = [];
    private static $conf;
    public static $register = [];
    private static $http = false;
    private static $once = false;

    public function __construct(){
    }

    public function __get($name){
        array_push(self::$apps,$name);
        return (new self);
    }

    public function __call($name,$value){
        array_push(self::$apps,$name);
        $file = implode('/',self::$apps);
        self::$apps = [];
        $nameApp = $file;
        $confFile = Path::$libs.'/'.$file.'.json';
        if(is_file($confFile)) {
          self::$conf = file_get_contents($confFile);
          self::$conf = json_decode(self::$conf,true);
        } else {
          self::$conf = [];
        }
        $file = Path::$libs.'/'.$file.'.php';
        if(!is_file($file)) return false;
        if(is_file($file)) {
          $func = require($file);
          if(is_callable($func)) {
            if(!isset(self::$register['once'][$nameApp])){
              $func = call_user_func_array($func,$value);
              self::$conf = [];
              if(self::$http === true) self::$register['http'][$nameApp] = true;
              if(self::$once === true) self::$register['once'][$nameApp] = $func;
              self::$http = false;
              self::$once = false;
            } else {
              $func = self::$register['once'][$nameApp];
            }
          }
          return $func;
        }
    }
}
?>
