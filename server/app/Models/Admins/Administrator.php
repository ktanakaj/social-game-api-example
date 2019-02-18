<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Exceptions\BadRequestException;
use App\Models\CamelcaseJson;

/**
 * 管理者モデル。
 */
class Administrator extends Authenticatable
{
    use SoftDeletes, CamelcaseJson;

    /**
     * モデルで使用するコネクション名。
     * @var string
     */
    protected $connection = 'admin';

    /**
     * 複数代入可能なプロパティ。
     * @var array
     */
    protected $fillable = [
        'email',
        'role',
        'note',
    ];

    /**
     * JSONへの変換結果に含めないカラム。
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        'role' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];
}
