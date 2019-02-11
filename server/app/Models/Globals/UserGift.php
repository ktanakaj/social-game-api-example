<?php

namespace App\Models\Globals;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
     * プレゼントを受け取る処理。
     * @var array
     */
    protected static $giftReceiveres = [];

    /**
     * タイムスタンプ更新を無効化。
     * @var bool
     */
    public $timestamps = false;

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'text_id',
        'text_options',
        'object_type',
        'object_id',
        'count',
    ];

    /**
     * 日付として扱う属性。
     * @var array
     */
    protected $dates = [
        'deleted_at',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'text_options' => 'array',
        'object_id' => 'integer',
        'count' => 'integer',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'text_options' => '{}',
    ];

    /**
     * モデルを作成する。
     */
    public function __construct(array $attributes = [])
    {
        // ※ updated_at だけがオフで SoftDeletes が有効だとエラーになるので暫定対処
        //    created_at もオフにして手動で日時指定
        parent::__construct($attributes);
        $this->created_at = new Carbon();
    }

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // ギフト全般でデフォルトのソート順を設定
        static::addGlobalScope('sortCreatedAt', function (Builder $builder) {
            return $builder->orderBy('user_id', 'asc')->orderBy('created_at', 'desc');
        });
    }

    /**
     * プレゼント受け取り処理を登録する。
     *
     * TODO: ↓整理する
     * クロージャは UserGift インスタンスを引数に取り、
     * 受け取ることができた場合、受け取り結果インスタンスを返さなければならない。
     * クロージャでは処理対象外の場合、nullを返す。
     * アイテム一杯などで受け取れない場合は例外を投げてよい。
     * @param \Closure $receiver プレゼント受け取り処理。
     */
    public static function addGiftReceiver(\Closure $receiver) : void
    {
        static::$giftReceiveres[] = $receiver;
    }

    /**
     * ユーザーとのリレーション定義。
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo('App\Models\Globals\User');
    }
}
