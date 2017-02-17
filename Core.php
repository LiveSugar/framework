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
        if(empty($html)) $html = ['index'];
        while($name = array_shift($html)){
          if(count($html) == 0){
            $view->{$name}();
          } else {
            $view = $view->{$name};
          }
        }
      $html = ob_get_clean();
      $html = preg_replace('/\>\s+\</Uui','><',$html);
      $html = preg_replace('/\s/Uui',' ',$html);
      $html = preg_replace('/[ ]+/Uui',' ',$html);
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

    // Js
      $config = $json(Path::$page.'/'.Path::$path);
      $content = '';
      // Config
      if(isset($config['js']) && is_array($config['js'])){
        foreach($config['js'] as $value){
          $file = Path::$js.'/'.$value.'.js';
          if(is_file($file)){
            $content .= file_get_contents($file);
            $content .= ';';
          }
        }
      }
      // Slave
      $slave = Path::$page.'/'.Path::$path.'/index.js';
      if(is_file($slave)){
        $content .= file_get_contents($slave);
        $content .= ';';
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
                $content .= ';';
              }
            }
          }
          $file = Path::$view.$path.'/index.js';
          if(!is_file($file)) continue;
          $content .= file_get_contents($file);
          $content .= ';';
        }
      }

      // Minify
      if(!empty($content)){
        $minifier = new JS();
        $minifier->add($content);
        $contentJs = $minifier->minify();
      } else {
        $contentJs = '';
      }

    // Css
    $config = $json(Path::$page.'/'.Path::$path);
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
    $slave = Path::$page.'/'.Path::$path.'/index.css';
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
      $contentCss = $minifier->minify();
    } else {
      $contentCss = '';
    }

    //
    // Page
    //
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

    //
    // Cotent
    //
    $content = '<!DOCTYPE html>'.
      '<html>'.
      '<head>'.
      '<title>'.$title.'</title>'.
      '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'.
      '<link rel="icon" type="image/x-icon" sizes="16x16" href="/favicon.ico">'.
      '<meta name ="Generator" Content="LiveSugare">'.
      '<meta name="description" Content="'.$description.'">'.
      '<meta name="keywords" Content="'.$keywords.'">'.
      '<meta name="robots" content="Index,follow">'.
      '<meta charset="utf-8">'.
      '<meta name="viewport" content="width=device-width, initial-scale=1">'.
      '<style type="text/css">'.$contentCss.'</style>'.
      //'<link rel="stylesheet" type="text/css" href="/css?path='.Path::$path.'">'.
      //'<script src="/js?path='.Path::$path.'" type="text/javascript"></script>'.
      '</head>'.
      '<body>'.$content.'<script type="text/javascript">'.$contentJs.'</script></body></html>';

    $content = preg_replace('/\>\s+\</Uui','><',$content);
    $content = preg_replace('/\s/Uui',' ',$content);
    $content = preg_replace('/[ ]+/Uui',' ',$content);
    die($content);
  }

}
?>
