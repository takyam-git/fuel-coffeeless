<?php
namespace CoffeeLess;

use Config;

class Asset extends \Fuel\Core\Asset
{
    public static function _init()
    {
        parent::_init();
        Config::load('coffeeless', true);
    }

    public static function coffee($coffee_scripts)
    {
        return static::instance()->coffee($coffee_scripts);
    }

    public static function less($less_files){
        return static::instance()->less($less_files);
    }
}