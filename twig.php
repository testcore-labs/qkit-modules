<?php
namespace core\modules; 
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use core\conf;
use controllers\user;
use controllers\video;
use core\modules\translation;

class twig {
    private static function twig() {
        $loader = new \Twig\Loader\FilesystemLoader(__FWDIR__."/templates");
        $twig = new \Twig\Environment($loader, ['debug' => true,]);

        $twig->addGlobal('conf', new conf());
        $twig->addGlobal('user', new user());
        $twig->addGlobal('video', new video());
        $twig->addGlobal('translation', new translate());
        $twig->addFilter(new \Twig\TwigFilter('translate', [new translate, 'translate']));
        $twig->addFilter(new \Twig\TwigFilter('json_decode', function ($string) {
            return json_decode($string, true);
        }));
        $twig->addFilter(new \Twig\TwigFilter('json_encode', function ($string) {
            return json_encode($string);
        }));
        $twig->addFilter(new \Twig\TwigFilter('timeago', function ($timeago) {
            return timeago($timeago ?? 0);
        }));
        $twig->addFunction(new \Twig\TwigFunction('file_get_contents', function ($url) {
            return file_get_contents($url);
        }));
        $twig->addFunction(new \Twig\TwigFunction('z_ws', function ($int = 1) {
            return str_repeat("​", $int);
        }));
        $twig->addFilter(new \Twig\TwigFilter('preg_replace', function ($subject, $pattern, $replacement) {
            return preg_replace($pattern, $replacement, $subject);
        }));
        #$twig->addGlobal('translator', translation::translator());
        $twig->addGlobal('cookie', $_COOKIE);
        $twig->addGlobal('session', $_SESSION);
        $twig->addGlobal('pagename', __PAGE__);
        $twig->addGlobal('url', __URL_NOQUERY__);
        $twig->addGlobal('domain', __DOMAIN__);

        $twig->addFunction(new \Twig\TwigFunction('header', function ($txt) {
        return header($txt);
        }));

        return $twig;
    }

    public static function view($file, $data = []) {
        $twig = self::twig();

        if (!preg_match('/\.twig$/', $file)) {
        $file .= '.twig';
        }
        
        if(file_exists(__FWDIR__."/templates".$file)) {
        return "Template does not exist.";
        }        

        try {
        return $twig->render($file, $data);
        } catch(\Exception $er) {
        return "twig didn't render: ". $er;
        }
    }
}
