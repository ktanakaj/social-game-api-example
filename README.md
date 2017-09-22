# ゲームのユーザーデータの試験的実装場
ソシャゲなどを想定したゲームのユーザーモデルの実装を試行錯誤している作業場です。

[Laravel 5](http://laravel.jp/) を使って試験的にモデルと機能を作成。

## 開発環境
* Vagrant 1.9.x - 仮想環境管理
    * VirtualBox 5.1.x - 仮想環境
    * vagrant-vbguest - Vagrantプラグイン

## 開発メモ
トップページにアクセスするとSwagger-UIのAPIデバック用ページが表示されます。

以下のコマンドが使用可能です（いずれも `game_svr` ディレクトリにて実行）。

* `php artisan migrate:refresh` : DB再作成
* `./vendor/bin/swagger -o ./public/api-docs.json ./app/` : Swagger用JSON定義再作成

※ Swagger再作成後にエラーになる場合はキャッシュが壊れた可能性があります。一度JSONファイルを消して、再実行してみてください。

## ライセンス
[MIT](https://github.com/ktanakaj/user_model_sandbox/blob/master/LICENSE)
