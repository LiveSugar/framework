<?php
namespace livesugar\framework;

use zz\Html\HTMLMinify;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

class Core {

  public function __construct($dir){
    (new Path($dir));
  }

  public function __destruct(){

    // Json
    $json = function($path) {
      $json = Path::$page.'/'.$path.'/index.json';
      if(is_file($json)){
        $json = file_get_contents($json);
        $json = json_decode($json,true);
      }
      return $json;
    };

    // API
    if(substr($_SERVER['HTTP_ACCEPT'],0,6) == 'api://'){
      header('Content-Type: application/json');
      die((new Exec(substr($_SERVER['HTTP_ACCEPT'],6),file_get_contents('php://input')))->json());
    }

    // Type Content
    switch(Path::$path){
      case "script.js":
        $type = 'js';
      break;
      case "style.css":
        $type = 'css';
      break;
      default:
        $type = 'page';
    }

    // Js
    if($type == 'js'){
      header("Content-Type: text/javascript");
      $json = $json(Path::$referer);
      $content = '';
      // Config
      if(isset($json['js']) && is_array($json['js'])){
        foreach($json['js'] as $value){
          $file = Path::$js.'/'.$value.'.js';
          if(is_file($file)){
            $content .= file_get_contents($file);
          }
        }
      }
      // Slave
      $slave = Path::$page.'/'.Path::$referer.'/index.js';
      if(is_file($slave)){
        $content .= file_get_contents($slave);
      }
      // Minify
      if(!empty($content)){
        $minifier = new JS();
        $minifier->add($content);
        $content = $minifier->minify();
      }
      die($content);
    }

    // Css
    if($type == 'css'){
      header("Content-Type: text/css");
      $json = $json(Path::$referer);
      $content = '';
      // Config
      if(isset($json['css']) && is_array($json['css'])){
        foreach($json['css'] as $value){
          $file = Path::$css.'/'.$value.'.css';
          if(is_file($file)){
            $content .= file_get_contents($file);
          }
        }
      }
      // Slave
      $slave = Path::$page.'/'.Path::$referer.'/index.css';
      if(is_file($slave)){
        $content .= file_get_contents($slave);
      }
      // Minify
      if(!empty($content)){
        $minifier = new CSS();
        $minifier->add($content);
        $content = $minifier->minify();
      }
      die($content);
    }

    // Page
    $content = '';
    ob_start();
    (new Page);
    $content = ob_get_clean();
    $content = '<!DOCTYPE html>'.
      '<html lang="ru">'.
      '<head>'.
      '<title></title>'.
      '<meta charset="utf-8">'.
      '<meta name="viewport" content="width=device-width, initial-scale=1">'.
      '<link rel="stylesheet" type="text/css" href="/style.css">'.
      '<script async src="/script.js" type="text/javascript"></script>'.
      '</head>'.
      '<body>'.$content.'</body></html>';

    $minify = new HTMLMinify($content,[HTMLMinify::OPTIMIZATION_ADVANCED,HTMLMinify::DOCTYPE_HTML5]);
    $content = $minify->process();
    die($content);
  }

}
?>
