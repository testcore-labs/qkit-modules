<?php
namespace core\modules; 
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;
use core\conf;

class translate {
 private static $translator;
 
 public static function getfile($locale) {
  return __TRANSDIR__.'/'.$locale.'.yml';
 }
 public static function init() {
  if (isset($_COOKIE['lang']) && preg_match('/^[a-z]{2}$/', $_COOKIE['lang'])) {
   $lang = $_COOKIE['lang'];
  } else {
   $_COOKIE['lang'] = "en";
   $lang = conf::get()['fw']['lang'];
  }
   self::$translator = new Translator($lang);
   self::$translator->addLoader('yaml', new YamlFileLoader());
   $files = scandir(__TRANSDIR__.'/');
   self::$translator->addResource('yaml', self::getfile("en"), 'en');
   foreach($files as $file) {
    $file = str_replace(".yml", "", $file);
    if($file !== "en") {
    self::$translator->addResource('yaml', self::getfile($file), $file);
    }
   }
  }

  public function getalllocales() {
   return self::translator->getFallbackLocales();
  }

 public static function translator() {
  if (self::$translator === null) {
   self::init();
  }
   return self::$translator;
 }

 public static function translate($key, $parameters = []) {
  if (self::$translator === null) {
   self::init();
  }
  return self::$translator->trans($key, $parameters);
 }
 public static function setlocale($locale) {
  return self::$translator->setLocale($locale);
 }
 public static function getlocale() {
  return self::$translator->getLocale();
 }

 public static function transcompletion($locale) {
  $keys1 = Yaml::parse(file_get_contents(self::getfile("en")));
  $keys2 = Yaml::parse(file_get_contents(self::getfile($locale)));

  $shit = array_intersect_key($keys1, $keys2);
  // to anyone reading this mess :skull:, sorry, this will round it up so it doesnt do 53.53525232355233%
  return round(round(count($shit)) / count($keys1) * 100, 2, PHP_ROUND_HALF_UP);
 }
}

translate::init();
