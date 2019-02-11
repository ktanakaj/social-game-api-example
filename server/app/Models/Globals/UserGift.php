<?php

namespace App\Models\Globals;

use Carbon\Carbon;
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
     * タイムスタンプ更新を無効化。
     * @var bool
     */
    public $timestamps = false;

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'text_id',
        'text_options',
        'gifts',
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
        'gifts' => 'array',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'text_options' => '{}',
        'gifts' => '{}',
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
     * ユーザーとのリレーション定義。
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo('App\Models\Globals\User');
    }
}
