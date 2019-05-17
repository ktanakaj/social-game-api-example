<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquentモデルで判定するexistsバリデーションルール。
 *
 * Laravel標準のexistsは直接テーブルを見るが、
 * モデルにキャッシュ等の機構を仕組んでいるため、モデルを使って処理させる。
 */
class ExistsByEloquent implements Rule
{
    /**
     * バリデーション条件のモデル名。
     * @var string
     */
    private $classname;

    /**
     * 指定された条件でルールのインスタンスを生成する。
     * @param string $classname Eloquentモデルクラス名。
     */
    public function __construct(string $classname)
    {
        if (!class_exists($classname) || !is_subclass_of($classname, Model::class)) {
            throw new \InvalidArgumentException("{$classname} is not Eloquent model");
        }
        $this->classname = $classname;
    }

    /**
     * バリデーション実施。
     * @param string $attribute パラメータのキー。
     * @param mixed $value パラメータの値。
     * @return bool バリデーションOKの場合true。
     */
    public function passes($attribute, $value)
    {
        return !!$this->classname::find($value);
    }

    /**
     * バリデーションエラー時のメッセージを取得する。
     * @return string エラーメッセージ。
     */
    public function message()
    {
        // ※ 現状標準のexistsのメッセージを流用
        return trans('validation.exists');
    }
}
