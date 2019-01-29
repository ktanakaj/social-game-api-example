# ゲームのユーザーデータの試験的実装場
ソシャゲなどを想定したゲームのユーザーモデルの実装を試行錯誤している作業場です。

[Laravel 5](http://laravel.jp/) を使って試験的にモデルと機能を作成。

## 開発環境
* Vagrant 2.2.x - 仮想環境管理
    * VirtualBox 5.2.x - 仮想環境
    * vagrant-vbguest - Vagrantプラグイン
    * vagrant-winnfsd - 〃

## 開発メモ
トップページにアクセスするとSwagger-UIのAPIデバック用ページが表示されます。

以下のコマンドが使用可能です（`server` ディレクトリにて実行）。

* `composer migrate` : DB作成
* `composer migrate:refresh` : DB再作成
* `php artisan db:seed` : 初期データ生成
* `composer test` : ユニットテスト
* `composer lint` : スタイルチェック

## ライセンス
[MIT](https://github.com/ktanakaj/user-model-sandbox/blob/master/LICENSE)
