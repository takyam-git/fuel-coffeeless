# FuelPHPでCoffeeScriptとLESSを使うためのPackageです
* NodeJSをインストールするのが面倒くさいPHPerのために、[coffeescript-php (https://github.com/alxlit/coffeescript-php)](https://github.com/alxlit/coffeescript-php)を使うことで、PHP単体でCoffeeScriptのコンパイルが行えるようになっています。
* 同じように[lessphp (http://leafo.net/lessphp/)](http://leafo.net/lessphp/)を使うことで、PHP単体でLESSのコンパイルが行えるようになっています。
* 特定のディレクトリ（例えば APPPATH . 'coffee' や APPPATH . 'less'）以下に、CoffeeScript/LESSファイルを作成し、Template側でAsset::coffee(), Asset::less()すればコンパイル後のJS/CSSへリンクしたscript/linkタグを出力します。
* 現状は、キャッシュ化などはしてません。そのうち実装します。
* デフォの設定だと、DOCROOT . 'assets/js_compiled' にコンパイル後のJSが出力されます。
* 同じく DOCROOT . 'assets/css_compiled' にコンパイル後のCSSが出力されます。
* Configファイルをコピーすることで変更できると思います。
* テストまだ作ってないです(´・ω・｀)

## Install
* とりあえずPackagesディレクトリにcloneしてください

```
$ cd /path/to/fuel_root
$ git clone https://github.com/takyam-git/fuel-coffeeless.git fuel/packages/coffeeless
$ cd fuel/packages/coffeeless
$ git submodule init
$ git submodule update

```
* Configを変えたい場合はコピーして変更してください

```
$ cd /path/to/fuel_root/fuel/
$ cp packages/coffeeless/config/coffeeless.php app/config/coffeeless.php
```
* CoffeeScript/LESSを置くディレクトリを作成してください

```
$ mkdir /path/to/fuel_root/fuel/app/coffee
$ mkdir /path/to/fuel_root/fuel/app/less
```

* コンパイル後のJSを置くディレクトリを作成してください(PHPから書き込めるようにしておいてください)

```
$ mkdir /path/to/fuel_root/public/assets/js_compiled
$ chmod -R 777 /path/to/fuel_root/public/assets/js_compiled
$ mkdir /path/to/fuel_root/public/assets/css_compiled
$ chmod -R 777 /path/to/fuel_root/public/assets/css_compiled
```

* View側で出力してください

```
$ vi /path/to/fuel_root/fuel/app/views/template.php
<?php echo Asset::coffee(array('test', 'pages/*', 'hoge/fuga/*', 'hoge/fuga/piyo')); ?>
// この場合にコンパイルされるのは以下
// coffee/test.coffee  -> js_compiled/test.js
// coffee/pages/*.coffee -> js_compiled/pages.js
// coffee/hoge/fuga/*.coffee -> js_compiled/hoge/fuga.js
// coffee/hoge/fuga/piyo.coffee -> js_compiled/hoge/fuga/piyo.js
<?php echo Asset::coffee(array('main.less', 'fuga.less')); ?>
```

みたいな感じです。   
とりあえず急いで作った感じなので機能は不足しまくってますが、とりあえず動きます。たぶん。

## 予定
* production時にGoogleClosureCompilerでMinimal化して出力する
* production時に都度コンパイルしないようにする
* development時もファイルの更新があった時だけコンパイルするようにする

## release notes
* 2012/10/11
 * fuel-coffee から fuel-coffeeless に名前を変更して、LESSにも対応
* 2012/10/08
 * first release