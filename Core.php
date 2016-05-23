<?php
namespace humanitysoft\humanity;

use zz\Html\HTMLMinify;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

class Core {

  public function __construct($dir){
    $this->dir = $dir;
  }

  public function __destruct(){
    $page = $this->dir.'/page/';
    $libs = $this->dir.'/libs/';
    $view = $this->dir.'/view/';
    $js = $this->dir.'/js/';
    $css = $this->dir.'/css/';
    $referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '' ;

    // Path
    $path = function($path){
      $path = parse_url($path)['path'];
      $path = urldecode($path);
      $path = explode('/',$path);
      foreach($path as $key=>$value) { $value = trim($value); if(empty($value)) { unset($path[$key]); } else { $path[$key] = $value; } }
      $path = array_values($path);
      if(empty($path)) $path = ['index'];
      $path = implode('/',$path);
      return $path;
    };

    // Json
    $json = function($path) use ($page) {
      $json = $page.'/'.$path.'/index.json';
      if(is_file($json)){
        $json = file_get_contents($json);
        $json = json_decode($json,true);
      }
      return $json;
    };


    // Type Content
    switch($path('//'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'])){
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
      $path = $path($referer);
      $json = $json($path);
      $content = '';
      // Config
      if(isset($json['js']) && is_array($json['js'])){
        foreach($json['js'] as $value){
          $file = $js.'/'.$value.'.js';
          if(is_file($file)){
            $content .= file_get_contents($file);
          }
        }
      }
      // Slave
      $slave = $page.'/'.$path.'/index.js';
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
      $path = $path($referer);
      $json = $json($path);
      $content = '';
      // Config
      if(isset($json['css']) && is_array($json['css'])){
        foreach($json['css'] as $value){
          $file = $css.'/'.$value.'.css';
          if(is_file($file)){
            $content .= file_get_contents($file);
          }
        }
      }
      // Slave
      $slave = $page.'/'.$path.'/index.css';
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
    $path = $path('//'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI']);
    $page = $page.'/'.$path.'/index.phtml';
    if(!is_file($page)) {
      http_response_code(404);
    } else {
      ob_start();
      (new Page($page,$libs,$view));
      $content = ob_get_clean();
    }
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
