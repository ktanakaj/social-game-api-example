<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CamelcaseJson;

/**
 * ユーザーのデッキの所属カードを表すモデル。
 */
class UserDeckCard extends Model
{
    use CamelcaseJson;

    /**
     * モデルのタイムスタンプを更新するかの指示。
     * @var bool
     */
    public $timestamps = false;

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_card_id',
        'position',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_deck_id' => 'integer',
        'user_card_id' => 'integer',
        'position' => 'integer',
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // デッキ全般でデフォルトのソート順を設定
        static::addGlobalScope('sortUsreAndNo', function (Builder $builder) {
            return $builder->orderBy('user_deck_id', 'asc')->orderBy('position', 'asc');
        });
    }

    /**
     * デッキとのリレーション定義。
     */
    public function deck() : BelongsTo
    {
        return $this->belongsTo('App\Models\Globals\UserDeck', 'user_deck_id');
    }

    /**
     * カードとのリレーション定義。
     */
    public function card() : BelongsTo
    {
        return $this->belongsTo('App\Models\Globals\UserCard', 'user_card_id');
    }
}
