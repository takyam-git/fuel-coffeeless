<?php
Autoloader::add_core_namespace('Coffee');
Autoloader::add_core_namespace('CoffeeScript');
Autoloader::add_classes(array(
    // Alias to Coffeepress class
    'Coffee\\Coffee'                => __DIR__.'/classes/coffee.php',
    'CoffeeScript\\Init'            => __DIR__.'/vendor/coffeescript-php/src/CoffeeScript/Init.php',
    'CoffeeScript\\Compiler'       => __DIR__.'/vendor/coffeescript-php/src/CoffeeScript/Compiler.php',
));
\CoffeeScript\Init::load(__DIR__.'/vendor/coffeescript-php/src/CoffeeScript/');
