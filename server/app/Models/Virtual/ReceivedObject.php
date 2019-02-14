<?php

namespace App\Models\Virtual;

/**
 * ドロップ品やプレゼント受け取りの結果など、ユーザーが何か受け取ったものを表すモデル。
 */
class ReceivedObject implements \JsonSerializable
{
    /**
     * 受け取ったものの種別。
     * @var string
     */
    public $object_type;
    /**
     * 受け取ったもののID。
     * ※ IDを持たない種別の場合null。
     * @var int
     */
    public $object_id = null;
    /**
     * 受け取った件数。
     * @var int
     */
    public $count = 1;
    /**
     * 受け取った後の件数。
     * 例）所持金1000で100受け取ったら1100。カードが自動合成されたら合成後の経験値。
     * @var int
     */
    public $total = null;
    /**
     * 初めて獲得したものか？
     * @var bool
     */
    public $is_new = false;

    /**
     * モデルを作成する。
     * @param array $attributes 連想配列でプロパティを設定する場合その値。
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * JSONシリアライズ用のデータを取得する。
     * @return array JSONシリアライズ可能なデータ。
     */
    public function jsonSerialize() : array
    {
        return (array)$this;
    }
}
