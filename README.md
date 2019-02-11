# Laravel 5 APIサンプルアプリ
[Laravel 5](http://laravel.jp/)のAPI勉強用に作成したサンプルアプリです。

クエストとかカードとかアイテムとかアチーブメントとかがあるようなソシャゲをイメージして作成しています。  
スマホアプリで、管理画面があって…みたいな想定だけど、お勉強用なので一部APIしかありません。

## 開発環境
* Vagrant 2.2.x - 仮想環境管理
    * VirtualBox 5.2.x - 仮想環境
    * vagrant-vbguest - Vagrantプラグイン
    * vagrant-winnfsd - 〃

## 開発メモ
トップページにアクセスするとSwagger-UIのAPIページが表示されます。

以下のコマンドが使用可能です（`server` ディレクトリにて実行）。

* `composer migrate` : DB作成
* `composer migrate:refresh` : DB再作成
* `php artisan db:seed` : 初期データ生成
* `composer test` : ユニットテスト
* `composer lint` : スタイルチェック

## ライセンス
[MIT](https://github.com/ktanakaj/laravel-api-example/blob/master/LICENSE)
