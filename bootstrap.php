<?php
require_once(__DIR__ . '/vendor/lessphp/lessc.inc.php');

Autoloader::add_core_namespace('CoffeeLess');
Autoloader::add_core_namespace('CoffeeScript');
Autoloader::add_classes(array(
    // Alias to Coffeepress class
    'CoffeeLess\\Asset' => __DIR__ . '/classes/asset.php',
    'CoffeeLess\\Asset_Instance' => __DIR__ . '/classes/asset/instance.php',
    'CoffeeScript\\Init' => __DIR__ . '/vendor/coffeescript-php/src/CoffeeScript/Init.php',
    'CoffeeScript\\Compiler' => __DIR__ . '/vendor/coffeescript-php/src/CoffeeScript/Compiler.php',
));
\CoffeeScript\Init::load(__DIR__ . '/vendor/coffeescript-php/src/CoffeeScript/');