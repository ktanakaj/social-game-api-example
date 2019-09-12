# Laravel勉強用ソシャゲAPIサンプルアプリ
[Laravel](http://laravel.jp/)のAPI勉強用に作成したサンプルアプリです。

クエストとかカードとかアイテムとかアチーブメントとかがあるようなソシャゲをイメージして作成しています。  
スマホアプリで、管理画面があって…みたいな想定だけど、お勉強用なので一部APIしかありません。

## 開発環境
* [Vagrant](https://www.vagrantup.com/) 2.2.4 - 仮想環境管理
    * Hyper-V (Windows 10 Pro) - 仮想環境

※ [VirtualBox](https://www.virtualbox.org/) + [vagrant-vbguest](https://github.com/dotless-de/vagrant-vbguest) + [vagrant-winnfsd](https://github.com/winnfsd/vagrant-winnfsd)プラグイン等でも動作します。

## 開発メモ
VMのトップページにアクセスするとSwagger-UIのAPIページが表示されます。

以下のコマンドが使用可能です（`server` ディレクトリにて実行）。

* `composer migrate` : DB作成
* `composer migrate:refresh` : DB再作成
* `php artisan db:seed` : 初期データ生成
* `php artisan master:import` : CSVマスタインポート
* `composer test` : ユニットテスト
* `composer lint` : スタイルチェック

## ライセンス
[MIT](https://github.com/ktanakaj/social-game-api-example/blob/master/LICENSE)
