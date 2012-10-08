# FuelPHPでCoffeeScriptを使うためのPackageです
* NodeJSをインストールするのが面倒くさいPHPerのために、[coffeescript-php (https://github.com/alxlit/coffeescript-php)](https://github.com/alxlit/coffeescript-php)を使うことで、* PHP単体でCoffeeScriptのコンパイルが行えるようになっています。
* 特定のディレクトリ（例えば APPPATH . 'coffee'）以下に、CoffeeScriptファイルを作成し、Template側でAssetのようにCoffee::output()すればコンパイル後のJSへリンクしたscriptタグを出力します。
* 現状は、キャッシュ化などはしてません。そのうち実装します。
* デフォの設定だと、DOCROOT . 'assets/js_compiled' にコンパイル後のJSが出力されます。
* Configファイルを作成することで変更できると思います。
* テストまだ作ってないです(´・ω・｀)

## Install
* とりあえずPackagesディレクトリにcloneしてください

```
$ cd /path/to/fuel_root
$ git clone https://github.com/takyam-git/fuel-coffee.git fuel/packages/coffee
$ cd fuel/packages/coffee
$ git submodule init
$ git submodule update

```
* Configを変えたい場合はコピーして変更してください

```
$ cd /path/to/fuel_root/fuel/
$ cp packages/coffee/config/coffee.php app/config/coffee.php
```
* CoffeeScriptを置くディレクトリを作成してください

```
$ mkdir /path/to/fuel_root/fuel/app/coffee
```

* コンパイル後のJSを置くディレクトリを作成してください(PHPから書き込めるようにしておいてください)

```
$ mkdir /path/to/fuel_root/public/assets/js_compiled
$ chmod -R 777 /path/to/fuel_root/public/assets/js_compiled
```

* View側で出力してください

```
$ vi /path/to/fuel_root/fuel/app/views/template.php
<?php echo Coffee::output(array('test', 'pages/*', 'hoge/fuga/*', 'hoge/fuga/piyo')); ?>
// この場合にコンパイルされるのは以下
// coffee/test.coffee  -> js_compiled/test.js
// coffee/pages/*.coffee -> js_compiled/pages.js
// coffee/hoge/fuga/*.coffee -> js_compiled/hoge/fuga.js
// coffee/hoge/fuga/piyo.coffee -> js_compiled/hoge/fuga/piyo.js
```

みたいな感じです。   
とりあえず急いで作った感じなので機能は不足しまくってますが、とりあえず動きます。

## 予定
* production時にGoogleClosureCompilerでMinimal化して出力する
* production時に都度コンパイルしないようにする
* development時もファイルの更新があった時だけコンパイルするようにする