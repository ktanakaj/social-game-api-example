<?php

namespace App\Models\Globals;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CamelcaseJson;
use App\Models\ObjectReceiver;
use App\Models\Virtual\ReceivedInfo;

/**
 * プレゼントデータを表すモデル。
 *
 * プレゼントボックスには、アイテムやゲームコインなどが一時的に格納される。
 * 古いデータは削除される。
 */
class UserGift extends Model
{
    use SoftDeletes, CamelcaseJson;

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
        'count' => 1,
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
            return $builder->orderBy('user_id', 'asc')->orderBy('created_at', 'desc')->orderBy('id', 'asc');
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
     * プレゼントを受け取る。
     * @return ReceivedInfo プレゼントの受け取り結果。
     */
    public function receive() : ReceivedInfo
    {
        $result = ObjectReceiver::receive($this->user_id, $this);
        $this->delete();
        return $result;
    }
}
