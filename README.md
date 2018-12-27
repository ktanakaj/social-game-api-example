# ゲームのユーザーデータの試験的実装場
ソシャゲなどを想定したゲームのユーザーモデルの実装を試行錯誤している作業場です。

[Laravel 5](http://laravel.jp/) を使って試験的にモデルと機能を作成。

## 開発環境
* Vagrant 2.1.1 - 仮想環境管理
    * VirtualBox 5.2.12 - 仮想環境
    * vagrant-vbguest - Vagrantプラグイン
    * vagrant-winnfsd - 〃

## 開発メモ
トップページにアクセスするとSwagger-UIのAPIデバック用ページが表示されます。

以下のコマンドが使用可能です（`game_svr` ディレクトリにて実行）。

* `php artisan migrate:refresh` : DB再作成

## ライセンス
[MIT](https://github.com/ktanakaj/user_model_sandbox/blob/master/LICENSE)
