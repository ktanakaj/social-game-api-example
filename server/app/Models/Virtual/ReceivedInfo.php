<?php

namespace App\Models\Virtual;

/**
 * ドロップ品やプレゼント受け取りの結果など、ユーザーが何か受け取ったものを表すモデル。
 */
class ReceivedInfo extends ObjectInfo
{
    /**
     * 受け取った後の件数。
     * 例）所持金1000で100受け取ったら1100。
     * @var int
     */
    public $total = null;
    /**
     * 初めて獲得したものか？
     * @var bool
     */
    public $is_new = false;
}
