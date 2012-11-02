<?php

namespace CoffeeLess;

use Config;
use lessc;
use Log;
use Exception;
use CoffeeScript;

/**
 * CoreのAssetクラスに less と coffee メソッドを追加している処理の実行インスタンス
 * Asset::coffe() or Asset::less() メソッドの呼び出しから、
 * この \CoffeeLess\Asset_Instance がシングルトンでよばれ、それぞれのメソッドが実行される
 */
class Asset_Instance extends \Fuel\Core\Asset_Instance
{
    private static $less_compiler = null;   //lessコンパイラ(lessphp)のシングルトン用インスタンス保持変数

    /**
     * Lessのコンパイラ（lessphp)のインスタンスを取得し、
     * シングルトンで返す
     * about lessphp >> http://leafo.net/lessphp/
     * @access public
     * @return \lessc|null
     */
    public function get_less_compiler(){
        if(is_null(self::$less_compiler)){
            self::$less_compiler = new lessc();
        }

        return self::$less_compiler;
    }

    /**
     * lessファイルパスを渡すと、コンパイルし、
     * コンパイル結果のCSSへの<link>タグを返す
     * @param array|string $less_filesc
     * @return string
     */
    public function less($less_files){
        $lesses = array();
        if(is_array($less_files)){
            foreach($less_files as $less_file){
                $lesses[] = $this->compile_less($less_file);
            }
        }else{
            $lesses[] = $this->compile_less($less_files);
        }

        $output = '';
        $attr = array(
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'href' => '',
        );
        foreach ($lesses as $less) {
            if (empty($less)) {
                continue;
            }
            $attr['href'] = $less;
            $output .= html_tag('link', $attr) . PHP_EOL;
        }
        return $output;
    }

    /**
     * lessのファイル名を渡すと、コンパイルして、コンパイル結果のCSSへの相対パスを返す
     * @param string $less_file_name
     * @param bool $add_file_mtime 相対パスの末尾にファイル変更日時を追加する場合は true
     * @return bool|string
     * @throws \Exception
     */
    public function compile_less($less_file_name, $add_file_mtime = true){
        $base_dir = Config::get('coffeeless.less.src_dir_base');
        $output_dir = Config::get('coffeeless.less.output_dir_base');
        $link_base = Config::get('coffeeless.less.link_path_base');

        try{
            if(substr($less_file_name, -5, 5) !== '.less'){
                $less_file_name .= '.less';
            }

            $less_path = $base_dir . DS . $less_file_name;
            if(!file_exists($less_path)){
                throw new Exception("Less file [${less_file_name}] is not exists.");
            }

            $less = $this->get_less_compiler();
            $compiled_css = $less->compileFile($less_path);

            if(!is_string($compiled_css) && empty($compiled_css)){
                throw new Exception("Less file [${less_file_name}] compile failed.");
            }

            $export_file_name = preg_replace('/\.less$/', '', $less_file_name) . '.css';
            $export_file_path = $output_dir . DS . $export_file_name;

            $export_file_dir = dirname($export_file_path);
            if(!is_dir($export_file_dir)){
                if (!mkdir($export_file_dir, 0777, true)) {
                    throw new Exception("Directory '${export_file_dir}' is not exists.'");
                }
            }

            file_put_contents($export_file_path, $compiled_css);

            return $link_base . DS . $export_file_name . ($add_file_mtime? '?' . filemtime($less_path) : '');

        }catch(Exception $error){
            Log::error('CoffeeLess package [compile_less] method error :: ' . $error->getMessage());
            return false;
        }
    }

    /**
     * CoffeeScriptファイル(.coffee)のパスか、
     * CoffeeScriptが格納されてるディレクトリのパスを渡すと、コンパイルし、
     * コンパイル結果のJSの<script>タグを返す
     *
     * 渡す値と実行結果例：
     * hoge/*  =>   hoge/001_fuga.coffee, hoge/002_piyo.coffee をマージして hoge.js にして返す
     * hoge => hoge.coffee を hoge.js として返す
     * hoge.coffee => hoge.js として返す
     * hoge/fuga/* => hoge/fuga/001.coffee, hoge/fuga/002.coffee をマージして hoge/fuga.js として返す
     * hoge/fuga.coffee => hoge/fuga.js として返す
     * hoge/fuga/piyo.coffee => hoge/fuga/piyo.js として返す
     * hoge/fuga/piyo => hoge/fuga/piyo.coffee を hoge/fuga/piyo.js として返す
     *
     * @param array|string $coffee_scripts
     * @return string
     */
    public function coffee($coffee_scripts)
    {
        $javascripts = array();
        if (is_array($coffee_scripts)) {
            foreach ($coffee_scripts as $coffee_script) {
                $javascripts[] = $this->compile_coffee($coffee_script);
            }
        } else {
            $javascripts[] = $this->compile_coffee($coffee_scripts);
        }

        $output = '';
        $attr = array(
            'type' => 'text/javascript',
            'src' => '',
        );
        foreach ($javascripts as $javascript) {
            if (empty($javascript)) {
                continue;
            }
            $attr['src'] = $javascript;
            $output .= html_tag('script', $attr, '') . PHP_EOL;
        }
        return $output;
    }

    /**
     * coffeeのファイル名を渡すと、コンパイルして、コンパイル結果のJSへの相対パスを返す
     * @param string $src
     * @param bool $add_file_mtime
     * @return bool|string
     * @throws \Exception
     */
    public function compile_coffee($src, $add_file_mtime = true)
    {
        $src = preg_replace('/\.coffee$/', '', $src);
        $src_path = Config::get('coffeeless.coffee.src_dir_base') . DS . $src;
        $js = preg_replace('/\/\*$/', '', $src) . '.js';
        $compiled = Config::get('coffeeless.coffee.output_dir_base') . DS . $js;

        try {

            $coffee = '';

            //ディレクトリ指定の場合
            if (substr($src_path, -1, 1) === '*') {
                $dir_path = dirname($src_path);
                if (!is_dir(dirname($src_path))) {
                    throw new Exception("Directory '${src_path}' is not exists.'");
                }

                $dir = opendir($dir_path);
                $coffee_script_paths = array();
                while ($file = readdir($dir)) {
                    if (substr($file, -7, 7) === '.coffee') {
                        $coffee .= file_get_contents($dir_path . DS . $file) . PHP_EOL;
                        $coffee_script_paths[] = $dir_path . DS . $file;
                    }
                }

                //ファイル名でソート
                asort($coffee_script_paths);
                foreach($coffee_script_paths as $coffee_script_path){
                    $coffee .= file_get_contents($coffee_script_path) . PHP_EOL;
                }

                //単一Coffeeファイルの場合
            } else {
                if (!file_exists($src_path . '.coffee')) {
                    throw new Exception();
                }
                $coffee = file_get_contents($src_path . '.coffee');
            }

            if (empty($coffee)) {
                throw new Exception();
            }

            //CoffeeScript(s) をJSにコンパイル（文字列として取得）
            $compiled_js = \CoffeeScript\Compiler::compile($coffee);

            //出力先ディレクトリの確認
            $compiled_dir = dirname($compiled);
            if (!is_dir($compiled_dir)) {
                if (!mkdir($compiled_dir, 0777, true)) {
                    throw new Exception("Directory '${$compiled_dir}' is not exists.'");
                }
            }

            //コンパイルしたJSをファイルで出力
            file_put_contents($compiled, $compiled_js);

        } catch (Exception $error) {
            Log::error('CoffeeLess package [compile_less] method error :: ' . $error->getMessage());
            return false;
        }

        //JSの相対パス（ex: /assets/compiled_js/hoge.js)を返す
        return Config::get('coffeeless.coffee.link_path_base') . DS . $js . ($add_file_mtime? '?' . filemtime($compiled) : '');;
    }
}