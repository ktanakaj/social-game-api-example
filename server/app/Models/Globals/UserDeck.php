<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use App\Models\CamelcaseJson;

/**
 * ユーザーが構築したデッキを表すモデル。
 *
 * デッキには複数のカードが所属する。
 */
class UserDeck extends Model
{
    use CamelcaseJson;

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'no' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // デッキ全般でデフォルトのソート順を設定
        static::addGlobalScope('sortUsreAndNo', function (Builder $builder) {
            return $builder->orderBy('user_id', 'asc')->orderBy('no', 'asc');
        });
    }

    /**
     * ユーザーとのリレーション定義。
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo('App\Models\Globals\User');
    }

    /**
     * デッキのカード情報とのリレーション定義。
     */
    public function cards() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserDeckCard');
    }

    /**
     * デッキのカードを更新する。
     * @param array $cards カード情報配列。※差分不可
     * @return Collection UserDeckCard配列。
     */
    public function updateOrCreateCards(array $cards) : Collection
    {
        \DB::transaction(function () use ($cards, &$newCards) {
            // ※ 差分更新しようとすると、カードIDが入れ替わるパターンでUNIQUE制約に引っかかる恐れがあるので、
            //    差分があるものは全て一度deleteして最後に再登録
            $noDiffCards = [];
            $cardsByPositions = collect($cards)->keyBy('position');
            foreach ($this->cards()->lockForUpdate()->get() as $userDeckCard) {
                $input = $cardsByPositions->get($userDeckCard->position);
                if (!$input) {
                    $userDeckCard->delete();
                    continue;
                }

                $userDeckCard->fill($input);
                if ($userDeckCard->isDirty()) {
                    $userDeckCard->delete();
                } else {
                    $cardsByPositions->forget($userDeckCard->position);
                    $noDiffCards[] = $userDeckCard;
                }
            }
            $newCards = $this->cards()->createMany($cardsByPositions->values()->all())->merge($noDiffCards)->sortBy('position')->values();
        });
        return $newCards;
    }
}
