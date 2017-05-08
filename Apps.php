<?php
namespace livesugar\framework;

class Apps {

    private static $apps = [];
    private static $conf;
    public static $register = [];
    private static $http = false;
    private static $once = false;
    private static $file = false;
    private static $apis = false;
    private static $join = false;


    public function __construct(){
      self::$file = new File;
      self::$apis = new Apis;
      self::$join = new Join;
      self::$join = new Path;
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
          if(is_array($func)) {
            $opts = $func[1];
            $opts = explode('|',$opts);
            $opts = array_flip($opts);
            $func = $func[0];
          }
          if(is_callable($func)) {
            if(!isset(self::$register['single'][$nameApp])){
              $func = call_user_func_array($func,$value);
              self::$conf = [];
              if(isset($opts)){
                if(isset($opts['SINGLE'])) self::$register['apps'][$nameApp]['single'] = $func;
              }
            } else {
              $func = self::$register['apps'][$nameApp]['single'];
            }
          }
          return $func;
        }
    }
}
?>
