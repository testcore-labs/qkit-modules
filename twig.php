<?php
namespace core\modules; 
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use core\conf;
use core\modules\translation;

class twig {
    public static $exfiles = [];
    public static $classes = [];
    public static $variables = [];

    private static function twig() {
        $files = [__FWDIR__."/templates", __FWDIR__."/"];
        foreach(self::$exfiles as $exfile) {
            $files[] = $exfile;
        };
        $loader = new \Twig\Loader\FilesystemLoader($files);
        $twig = new \Twig\Environment($loader, [
            'cache' => __FWDIR__.'/cache/twig/',
            'debug' => true,
            'optimizations' => -1,
            //'auto_reload' => false, //production
            //'cache_lifetime' => 3600 * 24, // production
        ]);


        foreach(self::$classes as $key => $global) {
            $twig->addGlobal($key, $global);
        };
        $twig->addGlobal('conf', new conf());
        $twig->addGlobal('translation', new translate());
        $twig->addFilter(new \Twig\TwigFilter('translate', [new translate, 'translate']));
        $twig->addFilter(new \Twig\TwigFilter('trans', [new translate, 'translate']));
        $twig->addFilter(new \Twig\TwigFilter('t', [new translate, 'translate']));

        $twig->addFilter(new \Twig\TwigFilter('img2proxy', function ($url, $w = "", $h = "") {
            return img2proxy($url, $w, $h);
        }));

        $twig->addFilter(new \Twig\TwigFilter('int_shorten', function ($number, $precision = 3, $divisors = null) {
            return int_shorten($number, $precision, $divisors);
        }));

        $twig->addFilter(new \Twig\TwigFilter('search', function ($haystack, $needle) {
            if(!isset($needle)) {
            array_search($needle, $haystack);
            return $haystack;
            } else {
            return $haystack;
            }
        }));

        $twig->addFilter(new \Twig\TwigFilter('tagsearch', function ($array, $search) {
            $tags = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            $filtered = array_filter($array, function ($item) use ($tags) {
                if (is_array($item)) {
                    $item = implode(' ', $item);
                }
                foreach ($tags as $tag) {
                    if (preg_match('/'.$tag.'/i', $item)) {
                        return true;
                    }
                }
                return false;
            });
    
            return $filtered;
        }));
        $twig->addFilter(new \Twig\TwigFilter('shuffle', function ($array) {
            shuffle($array);
            return $array;
        }));
        $twig->addFilter(new \Twig\TwigFilter('json_decode', function ($string) {
            return json_decode($string, true);
        }));
        $twig->addFilter(new \Twig\TwigFilter('json_encode', function ($string) {
            return json_encode($string);
        }));

        $twig->addFilter(new \Twig\TwigFilter('url_encode', function ($string) {
            return urlencode($string);
        }));
        $twig->addFilter(new \Twig\TwigFilter('url_decode', function ($string) {
            return urldecode($string);
        }));


        $twig->addFunction(new \Twig\TwigFunction('global', function ($class, $args = []) {
            if(class_exists($class)) {
            return new $class(...$args);
            } else {
            return false;
            }
        }));
        
        $twig->addFunction(new \Twig\TwigFunction('file_get_contents', function ($url) {
            return file_get_contents($url);
        }));
        $twig->addFunction(new \Twig\TwigFunction('getallheaders', function () {
            return getallheaders();
        }));
        $twig->addFunction(new \Twig\TwigFunction('z_ws', function ($int = 1) {
            return str_repeat("​", $int);
        }));
        $twig->addFilter(new \Twig\TwigFilter('timeago', function ($int = 0) {
            return @timeago($int);
        }));
        $twig->addFilter(new \Twig\TwigFilter('preg_replace', function ($subject, $pattern, $replacement) {
            return preg_replace($pattern, $replacement, $subject);
        }));
        #$twig->addGlobal('translator', translation::translator());
        $twig->addGlobal('fwdir', __FWDIR__);
        $twig->addGlobal('transdir', __TRANSDIR__);
        $twig->addGlobal('cookie', $_COOKIE);
        $twig->addGlobal('post', $_POST);
        $twig->addGlobal('get', $_GET);
        $twig->addGlobal('pagename', __PAGE__);
        $twig->addGlobal('url_query', __URL__);
        $twig->addGlobal('query', str_replace(__URL_NOQUERY__, "", __URL__));
        $twig->addGlobal('url', __URL_NOQUERY__);
        $twig->addGlobal('domain', __DOMAIN__);

        $twig->addFunction(new \Twig\TwigFunction('sleep', function ($secs) {
        return sleep($secs);
        }));

        $twig->addFunction(new \Twig\TwigFunction('microtime', function ($float = false) {
        return microtime($float);
        }));

        $twig->addFunction(new \Twig\TwigFunction('header', function ($txt) {
        return header($txt);
        }));

        $twig->addFunction(new \Twig\TwigFunction('redirect', function ($url) {
        return die(header("Location: $url"));
        }));

        $twig->addFunction(new \Twig\TwigFunction('vardump', function ($obj) {
        return var_dump($obj);
        }));

        $twig->addFunction(new \Twig\TwigFunction('http_response_code', function ($int = null) {
        return http_response_code($int) ?? 0;
        }));

        $twig->addFunction(new \Twig\TwigFunction('intval', function ($int, $base = 10) {
        return intval($int, $base);
        }));

        $twig->addFunction(new \Twig\TwigFunction('http_response_codename', function ($int) {
        return http_response_codename($int) ?? "";
        }));

        $twig->addFilter(new \Twig\TwigFilter('br2nl', function ($txt) {
            return preg_replace('#<br\s*/?>#i', PHP_EOL, $txt);
        }));
        
        $twig->addFilter(new \Twig\TwigFilter('ordinal', function ($int) {
            return ordinal($int);
        }));

        $twig->addFilter(new \Twig\TwigFilter('truncate', function ($text, $length, $ellipsis = "...") {
            return truncate($text, $length, $ellipsis);
        }));


        $twig->addFilter(new \Twig\TwigFilter('eval', function ($string, $data = []) use ($twig) {
            $template = $twig->createTemplate($string);
            return $template->render($data);
        }));
        return $twig;
    }

    public static function view($file, $data = []) {
        $twig = self::twig();

        if (!preg_match('/\.twig$/', $file)) {
        $file .= '.twig';
        }
        
        if(file_exists(__FWDIR__."/templates".$file)) {
        return "template does not exist";
        }        

        if(isset($data) && isset($variables)) {
        array_merge($data, $variables);
        }

        try {
        return $twig->render($file, $data);
        } catch(\Exception $er) {
        return $er;
        }
    }
}
