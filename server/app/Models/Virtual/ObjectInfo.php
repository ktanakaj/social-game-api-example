<?php

namespace App\Models\Virtual;

/**
 * 任意のオブジェクト（アイテムやカード、コインや経験値等）の情報を扱うモデル。
 */
class ObjectInfo implements \JsonSerializable
{
    /**
     * オブジェクトの種別。
     * @var string
     */
    public $type;
    /**
     * オブジェクトのID。
     * ※ IDを持たない種別の場合null。
     * @var int
     */
    public $id = null;
    /**
     * オブジェクトの数。
     * @var int
     */
    public $count = 1;

    /**
     * モデルを作成する。
     * @param array|object $attributes 連想配列またはオブジェクトでプロパティを設定する場合その値。
     */
    public function __construct($attributes = [])
    {
        // モデルもそのまま渡せるよう、全てインスタンスに統一して処理する
        // （連想配列に変換するのは専用のメソッドを呼ばないとおかしなことになるのでインスタンスにする）
        $params = $attributes;
        if (is_array($attributes)) {
            // キーがキャメルケースの場合、スネークケースに変換
            // ※ インスタンスの場合には変換していない。必要なら検討
            $params = new \stdClass();
            foreach ($attributes as $key => $value) {
                $key = snake_case($key);
                $params->{$key} = $value;
            }
        }
        // このインスタンスに存在する値を設定。
        // ただし object_type, object_id がある場合は、type, id ではなくそちらを用いる。
        $keys = array_keys(get_object_vars($this));
        $keys = array_combine($keys, $keys);
        if (isset($params->object_type)) {
            // ※ unsetしておかないと、idがnullの場合にissetで入らず変になる
            unset($keys['type']);
            unset($keys['id']);
            $keys += ['object_type' => 'type', 'object_id' => 'id'];
        }
        foreach ($keys as $key => $newkey) {
            if (isset($params->{$key})) {
                $this->{$newkey} = $params->{$key};
            }
        }
    }

    /**
     * JSONシリアライズ用のデータを取得する。
     * @return array JSONシリアライズ可能なデータ。
     */
    public function jsonSerialize() : array
    {
        // キーをキャメルケースに変換して返す
        $json = [];
        foreach (get_object_vars($this) as $key => $value) {
            $json[camel_case($key)] = $value;
        }
        return $json;
    }
}
