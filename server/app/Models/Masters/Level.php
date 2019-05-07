<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\InternalServerErrorException;
use App\Models\CamelcaseJson;

/**
 * レベルマスタを表すモデル。
 *
 * 各レベルに必要な経験値や最大スタミナを扱う。
 * ※ 現状ユーザーやカードでマスタ自体は共通。
 *    獲得経験値で調整すればよい想定だが、必要なら分ける。
 */
class Level extends MasterModel
{
    use CamelcaseJson;

    /**
     * プライマリーキー列。
     * @var string
     */
    protected $primaryKey = 'level';

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'level' => 'integer',
        'exp' => 'integer',
        'max_stamina' => 'integer',
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // マスタはデフォルトではレベルの昇順でソートする
        static::addGlobalScope('sortId', function (Builder $builder) {
            return $builder->orderBy('level', 'asc');
        });
    }

    /**
     * 経験値から該当するレベルを取得する。
     * @param int $exp 経験値。
     * @return Level 該当するレベル。
     * @throws InternalServerErrorException マスタにレベルが存在しない場合。
     */
    public static function findByExp(int $exp) : Level
    {
        // EXPをWHERE句に指定して検索するとキャッシュが効かないので、全件取得してPHP側で探索する。
        // （レベルマスタは件数が少ない前提。かつ、マスタキャッシュに加えリクエスト中は読み込み済みの配列を使い回す。）
        $all = \Cache::store('array')->rememberForever(self::class . ':arrayall', function () {
            return self::all();
        });

        $before = null;
        foreach ($all as $level) {
            if ($level->exp > $exp) {
                break;
            }
            $before = $level;
        }

        // ※ 最低でも経験値0のレベル1が登録されている想定
        if ($before) {
            return $before;
        } else {
            throw new InternalServerErrorException("Level is not found (exp=${exp})");
        }
    }
}
