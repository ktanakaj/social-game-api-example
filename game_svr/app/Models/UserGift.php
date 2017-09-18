<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * プレゼントデータを表すモデル。
 *
 * プレゼントボックスには、アイテムやゲームコインなどが一時的に格納される。
 * 古いデータは削除される。
 */
class UserGift extends Model
{
    use SoftDeletes;

    /**
     * ギフトを所有するユーザーを取得する。
     * @return User ユーザー。
     */
    public function user() : User
    {
        return $this->belongsTo('App\Model\User');
    }

    /**
     * ギフトメッセージのマスタを取得する。
     * @return GiftMessage アイテムマスタ。
     */
    public function message() : GiftMessage
    {
        return $this->belongsTo('App\Model\GiftMessage', 'message_id');
    }
}
