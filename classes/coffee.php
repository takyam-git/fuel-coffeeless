<?php
namespace Coffee;

use Config;
use Exception;
use CoffeeScript;

class Coffee
{
    public static function output($coffee_scripts)
    {
        Config::load('coffee', true);
        $src_dir = Config::get('coffee.src_dir_base');
        $output_dir = Config::get('coffee.output_dir_base');

        $javascripts = array();
        if (is_array($coffee_scripts)) {
            foreach ($coffee_scripts as $coffee_script) {
                $javascripts[] = self::compile($coffee_script);
            }
        } else {
            $javascripts[] = self::compile($coffee_scripts);
        }

        $output = '';
        foreach ($javascripts as $javascript) {
            if (empty($javascript)) {
                continue;
            }
            $output .= "<script type=\"text/javascript\" src=\"${javascript}\"></script>";
        }
        return $output;
    }

    public static function compile($src)
    {
        $src = preg_replace('/\.coffee$/', '', $src);
        $src_path = Config::get('coffee.src_dir_base') . DS . $src;
        $js = preg_replace('/\/\*$/', '', $src) . '.js';
        $compiled = Config::get('coffee.output_dir_base') . DS . $js;

        try {

            $coffee = '';

            //ディレクトリ指定の場合
            if (substr($src_path, -1, 1) === '*') {
                $dir_path = dirname($src_path);
                if (!is_dir(dirname($src_path))) {
                    throw new Exception();
                }

                $dir = opendir($dir_path);
                while($file = readdir($dir)){
                    if(substr($file, -7, 7) === '.coffee'){
                        $p = $dir_path . DS . $file;
                        $d = file_get_contents($p);
                        $coffee .= file_get_contents($dir_path . DS . $file) . PHP_EOL;
                    }
                }

            //単一Coffeeファイルの場合
            } else {
                if (!file_exists($src_path . '.coffee')) {
                    throw new Exception();
                }
                $coffee = file_get_contents($src_path . '.coffee');
            }

            if(empty($coffee)){
                throw new Exception();
            }

            //CoffeeScript(s) をJSにコンパイル（文字列として取得）
            $compiled_js = \CoffeeScript\Compiler::compile($coffee);

            //出力先ディレクトリの確認
            $compiled_dir = dirname($compiled);
            if (!is_dir($compiled_dir)) {
                if (!mkdir($compiled_dir, 0777, true)) {
                    throw new Exception();
                }
            }

            //コンパイルしたJSをファイルで出力
            file_put_contents($compiled, $compiled_js);

        } catch (Exception $error) {
            return false;
        }

        //JSの相対パス（ex: /assets/compiled_js/hoge.js)を返す
        return Config::get('coffee.link_path_base') . DS . $js;
    }
}