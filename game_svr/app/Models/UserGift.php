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
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'data' => '{}',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'message_id',
        'data',
    ];

    /**
     * `updated_at`の自動挿入の無効化。
     * @param mixed $value 更新日時。
     * @return UserGift $this
     */
    public function setUpdatedAt($value) : UserGift
    {
        return $this;
    }

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
