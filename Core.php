<?php
namespace livesugar\framework;

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
    // API[FILE]
    if(substr($_SERVER['HTTP_ACCEPT'],0,12) == 'api[file]://'){
      $file = file_get_contents('php://input');
      $fileTmp = tempnam(sys_get_temp_dir(), microtime(1));
      file_put_contents($fileTmp,$file);
      File::add($fileTmp);
      die((new Exec(substr($_SERVER['HTTP_ACCEPT'],12)))->json());
    }
    // VIEW
    if(substr($_SERVER['HTTP_ACCEPT'],0,7) == 'view://'){
      header('Content-Type: application/json');
      $path = substr($_SERVER['HTTP_ACCEPT'],7);
      $html = explode('/',$path);
      foreach($html as $key=>$value){
        $value = trim($value);
        if(empty($value)) unset($html[$key]);
      }
      $output = [];
      ob_start();
        $view = new View;
        while($name = array_shift($html)){
          if(count($html) == 0){
            $view->{$name}();
          } else {
            $view = $view->{$name};
          }
        }
      $html = ob_get_clean();
      $output['html'] = $html;
      $path = Path::$view.''.$path;
      $css = $path.'/index.css';
      if(is_file($css)) $output['css'] = file_get_contents($css);
      $js = $path.'/index.js';
      if(is_file($js)) $output['js'] = file_get_contents($js);
      die(json_encode($output,JSON_UNESCAPED_UNICODE));
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
      $config = $json(Path::$page.'/'.$_GET['path']);
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
      $slave = Path::$page.'/'.$_GET['path'].'/index.js';
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
      $config = $json(Path::$page.'/'.$_GET['path']);
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
      $slave = Path::$page.'/'.$_GET['path'].'/index.css';
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

    $title = Meta::$title;
    $title = implode(' &ndash; ', $title);

    $description = Meta::$description;

    $keywords = Meta::$keywords;
    $keywords = implode(', ', $keywords);

    $content = '<!DOCTYPE html>'.
      '<html>'.
      '<head>'.
      '<title>'.$title.'</title>'.
      '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'.
      '<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">'.
      '<meta name ="Generator" Content="LiveSugare">'.
      '<meta name="description" Content="'.$description.'">'.
      '<meta name="keywords" Content="'.$keywords.'">'.
      '<meta name="robots" content="Index,follow">'.
      '<meta charset="utf-8">'.
      '<meta name="viewport" content="width=device-width, initial-scale=1">'.
      '<link rel="stylesheet" type="text/css" href="/style.css?path='.Path::$path.'">'.
      '<script src="/script.js?path='.Path::$path.'" type="text/javascript"></script>'.
      '</head>'.
      '<body>'.$content.'</body></html>';

    $content = preg_replace('/\>\s+\</Uui','><',$content);
    $content = preg_replace('/\s/Uui',' ',$content);
    $content = preg_replace('/[ ]+/Uui',' ',$content);
    die($content);
  }

}
?>
