<?php
namespace livesugar\framework;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

class Core {

  public function __construct($dir){
    (new Path($dir));
  }

  public function __destruct(){

    if(isset($_SERVER['HTTP_ACCEPT'])){
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

    //
    // Save get
    //
    $save = Save::get();

    //
    // JavaScript
    //
    if(substr(Path::$path,0,32) == 'de9b9ed78d7e2e1dceeffee780e2f919'){
      $path = substr(Path::$path,33);
      $config = $json(Path::$page.'/'.$path);
      $contentJs = '';
      // Config
      if(isset($config['js']) && is_array($config['js'])){
        foreach($config['js'] as $value){
          $file = Path::$js.'/'.$value.'.js';
          if(is_file($file)){
            $contentJs .= file_get_contents($file);
            $contentJs .= ';';
          }
        }
      }
      // Slave
      $slave = Path::$page.'/'.$path.'/index.js';
      if(is_file($slave)){
        $contentJs .= file_get_contents($slave);
        $contentJs .= ';';
      }

      // Slave js
      if(isset($save['view'])){
        foreach($save['view'] as $path){
          $config = $json(Path::$view.'/'.$path);
          if(isset($config['js']) && is_array($config['js'])){
            foreach($config['js'] as $value){
              $file = Path::$js.'/'.$value.'.js';
              if(is_file($file)){
                $contentJs .= file_get_contents($file);
                $contentJs .= ';';
              }
            }
          }
          $file = Path::$view.$path.'/index.js';
          if(!is_file($file)) continue;
          $contentJs .= file_get_contents($file);
          $contentJs .= ';';
        }
      }

      // Minify
      if(!empty($contentJs)){
        $minifier = new JS();
        $minifier->add($contentJs);
        $contentJs = $minifier->minify();
      } else {
        $contentJs = '';
      }
      header('Content-Type: application/javascript');
      die($contentJs);
    }

    //
    // CSS
    //
    if(substr(Path::$path,0,32) == 'c7a628cba22e28eb17b5f5c6ae2a266a'){
      $path = substr(Path::$path,33);
      $config = $json(Path::$page.'/'.$path);
      $contentCss = '';
      // Config
      if(isset($config['css']) && is_array($config['css'])){
        foreach($config['css'] as $value){
          $file = Path::$css.'/'.$value.'.css';
          if(is_file($file)){
            $contentCss .= file_get_contents($file);
          }
        }
      }
      // Slave
      $slave = Path::$page.'/'.$path.'/index.css';
      if(is_file($slave)){
        $contentCss .= file_get_contents($slave);
      }

      // Slave view
      if(isset($save['view'])){
        foreach($save['view'] as $path){
          $config = $json(Path::$view.'/'.$path);
          if(isset($config['css']) && is_array($config['css'])){
            foreach($config['css'] as $value){
              $file = Path::$css.'/'.$value.'.css';
              if(is_file($file)){
                $contentCss .= file_get_contents($file);
              }
            }
          }
          $file = Path::$view.$path.'/index.css';
          if(!is_file($file)) continue;
          $contentCss .= file_get_contents($file);
        }
      }

      // Minify
      if(!empty($contentCss)){
        $minifier = new CSS();
        $minifier->add($contentCss);
        $contentCss = $minifier->minify();
      } else {
        $contentCss = '';
      }
      header("Content-type: text/css");
      die($contentCss);
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
      '<meta charset="utf-8">'.
      '<link rel="icon" type="image/x-icon" sizes="16x16" href="/favicon.ico">'.
      '<meta name ="Generator" Content="LiveSugare">'.
      '<link href="">'.
      '<meta name="description" Content="'.$description.'">'.
      '<meta name="keywords" Content="'.$keywords.'">'.
      '<meta name="robots" content="Index,follow">'.
      '<link rel="stylesheet" type="text/css" href="/c7a628cba22e28eb17b5f5c6ae2a266a/'.Path::$path.'">'.
      '<script src="/de9b9ed78d7e2e1dceeffee780e2f919/'.Path::$path.'" type="text/javascript"></script>'.
      '</head>'.
      '<body>'.$content.'</body></html>';

    $content = preg_replace('/\>\s+\</Uui','><',$content);
    $content = preg_replace('/\s/Uui',' ',$content);
    $content = preg_replace('/[ ]+/Uui',' ',$content);
    die($content);
  }

}
?>
