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
     * タイムスタンプ更新を無効化。
     * @var bool
     */
    public $timestamps = false;

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
     * モデルを作成する。
     */
    public function __construct(array $attributes = [])
    {
        // ※ updated_at だけがオフで SoftDeletes が有効だとエラーになるので暫定対処
        //    created_at もオフにして手動で日時指定
        parent::__construct($attributes);
        $this->created_at = new \DateTime();
    }

    /**
     * ギフトを所有するユーザーを取得する。
     * @return User ユーザー。
     */
    public function user() : User
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * ギフトメッセージのマスタを取得する。
     * @return GiftMessage アイテムマスタ。
     */
    public function message() : GiftMessage
    {
        return $this->belongsTo('App\Models\GiftMessage', 'message_id');
    }
}
