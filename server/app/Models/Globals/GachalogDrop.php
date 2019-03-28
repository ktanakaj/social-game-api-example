<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CamelcaseJson;

/**
 * ユーザーのガチャの排出物履歴を表すモデル。
 */
class GachalogDrop extends Model
{
    use CamelcaseJson;

    /** update_atの無効化。 */
    const UPDATED_AT = null;

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'object_type',
        'object_id',
        'count',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'gachalog_id' => 'integer',
        'object_id' => 'integer',
        'count' => 'integer',
        'created_at' => 'timestamp',
    ];

    /**
     * ガチャ履歴とのリレーション定義。
     */
    public function gachalog() : BelongsTo
    {
        return $this->belongsTo('App\Models\Globals\Gachalog');
    }
}
