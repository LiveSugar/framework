[![StyleCI Status](https://styleci.io/repos/150756823/shield)](https://github.styleci.io/repos/150756823)

# Documentation

## Install

With create directory to latest stability version 
```
composer create-project livesugar/framework
```

Current directory
```
composer create-project livesugar/framework .
```

Version of develop to current directory
```
composer create-project --prefer-dist  livesugar/framework . dev-master@dev
```

## Examples

Call apps to HTTP GET query
```
http://example.com?apps=uuid
```

Call view to HTTP GET query
```
http://example.com?view=page/index
```

Call apps to PHP file
```php
$this->nameapps();
# or 
$this->namespace->nameapps();
# or
$this->categoryapps->nameapps();
```

Call apps to PHTML file
```html
<div><?php echo self::$apps->nameapps() ?></div>
<div><?php echo self::$apps->nameclass->namemethod() ?></div>
```
Call view to PHTML file
```html
<div><?php echo self::$view->nameview(); ?></div>
```

## History
1. [WebCMF](https://github.com/xezzus/webcmf)
2. [WebCMF2](https://github.com/xezzus/webcmf2)
3. [WebCMF3](https://github.com/xezzus/webcmf3)
4. [Humanity](https://github.com/xezzus/humanity)
5. [LiveSugar (current)](https://github.com/LiveSugar/framework)
