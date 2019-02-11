<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Masters\Card;

/**
 * ユーザーが持つカードを表すモデル。
 *
 * ユーザーが保持するカードとその情報が格納される。
 */
class UserCard extends Model
{
    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'card_id',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'card_id' => 'integer',
        'count' => 'integer',
        'exp' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'count' => 1,
        'exp' => 0,
    ];

    /**
     * ユーザーとのリレーション定義。
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo('App\Models\Globals\User');
    }
}
