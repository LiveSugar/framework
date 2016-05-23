<?php
namespace humanitysoft\humanity;

class Apps {

    private static $application = [];
    private static $conf;
    private static $singleton = false;
    private static $dir = null;
    private static $register = [];
    private static $view;

    public function __construct($dir=null){
      if(!is_null($dir) && is_dir($dir)) self::$dir = $dir;
    }

    public function __get($name){
        array_push(self::$application,$name);
        return (new self);
    }

    public function __call($name,$value){
        array_push(self::$application,$name);
        $file = implode('/',self::$application);
        self::$application = [];
        $nameApp = $file;
        $confFile = self::$dir.'/'.$file.'.json';
        if(is_file($confFile)) {
          self::$conf = file_get_contents($confFile);
          self::$conf = json_decode(self::$conf,true);
        } else {
          self::$conf = [];
        }
        $file = self::$dir.'/'.$file.'.php';
        if(!is_file($file)) return false;
        if(is_file($file)) {
          $func = require($file);
          if(is_callable($func)) {
            if(!$singleton = (new Singleton)->get($nameApp)){
              $func = call_user_func_array($func,$value);
              self::$conf = [];
              if(self::$singleton === true) {
                (new Singleton)->set($nameApp,$func);
                self::$singleton = false;
              }
            } else {
              $func = $singleton;
            }
          }
          return $func;
        }
    }
}
?>
