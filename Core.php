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

    // API
    if(substr($_SERVER['HTTP_ACCEPT'],0,6) == 'api://'){
      header('Content-Type: application/json');
      die((new Exec(substr($_SERVER['HTTP_ACCEPT'],6),file_get_contents('php://input')))->json());
    }

    // Json
    $json = function($path) {
      $json = $path.'/index.json';
      if(is_file($json)){
        $json = file_get_contents($json);
        $json = json_decode($json,true);
      }
      return $json;
    };


    // Save get
    $save = Save::get();

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
      $config = $json(Path::$page.'/'.Path::$referer);
      $content = '';
      // Config
      if(isset($config['js']) && is_array($config['js'])){
        foreach($config['js'] as $value){
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

      // Slave js
      if(isset($save['view'])){
        foreach($save['view'] as $path){
          $config = $json(Path::$view.'/'.$path);
          if(isset($config['js']) && is_array($config['js'])){
            foreach($config['js'] as $value){
              $file = Path::$js.'/'.$value.'.js';
              if(is_file($file)){
                $content .= file_get_contents($file);
              }
            }
          }
          $file = Path::$view.$path.'/index.js';
          if(!is_file($file)) continue;
          $content .= file_get_contents($file);
        }
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
      $config = $json(Path::$page.'/'.Path::$referer);
      $content = '';
      // Config
      if(isset($config['css']) && is_array($config['css'])){
        foreach($config['css'] as $value){
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

      // Slave view
      if(isset($save['view'])){
        foreach($save['view'] as $path){
          $config = $json(Path::$view.'/'.$path);
          if(isset($config['css']) && is_array($config['css'])){
            foreach($config['css'] as $value){
              $file = Path::$css.'/'.$value.'.css';
              if(is_file($file)){
                $content .= file_get_contents($file);
              }
            }
          }
          $file = Path::$view.$path.'/index.css';
          if(!is_file($file)) continue;
          $content .= file_get_contents($file);
        }
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

    $save = Save::get();
    $save['view'] = Info::$view;
    Save::set($save);

    $content = '<!DOCTYPE html>'.
      '<html lang="ru">'.
      '<head>'.
      '<title></title>'.
      '<meta charset="utf-8">'.
      '<meta name="viewport" content="width=device-width, initial-scale=1">'.
      '<link rel="stylesheet" type="text/css" href="/style.css">'.
      '<script src="/script.js" type="text/javascript"></script>'.
      '</head>'.
      '<body>'.$content.'</body></html>';

    $minify = new HTMLMinify($content,[HTMLMinify::OPTIMIZATION_ADVANCED,HTMLMinify::DOCTYPE_HTML5]);
    $content = $minify->process();
    die($content);
  }

}
?>
