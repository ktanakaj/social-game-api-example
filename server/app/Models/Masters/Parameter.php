<?php

namespace App\Models\Masters;

/**
 * ゲームパラメータマスタモデル。
 *
 * マスタ管理の設定値を扱うマスタ。
 * .env とか config とかはアプリと一体の環境設定的で、
 * ゲーム固有の設定値（各種係数とか）の管理には向いていないので別途定義。
 */
class Parameter extends MasterModel
{
    /**
     * 主キーの「タイプ」。
     * @var string
     */
    protected $keyType = 'string';

    /**
     * 値を設定する。
     * @param mixed $value オリジナルの値。
     */
    public function setValueAttribute($value) : void
    {
        // マスタインポート用。後述のように$castsが動的に指定される都合上、
        // ミューテタが無いとJSON文字列が二重にエスケープされる恐れがあるので、
        // 空定義でそれを阻止する
        $this->attributes['value'] = $value;
    }

    /**
     * キャスト定義を取得する。
     * @return array キャスト定義。
     */
    public function getCasts()
    {
        // valueの型としてtypeを動的に追加する
        $casts = parent::getCasts();
        if (!empty($this->type)) {
            $casts['value'] = $this->type;
        }
        return $casts;
    }

    /**
     * パラメータを取得する。
     * @param string $id パラメータID。
     * @param mixed $default パラメータが存在しない場合のデフォルト値。
     * @return mixed パラメータの値。
     */
    public static function get(string $id, $default = null)
    {
        $param = self::find($id);
        return $param ? $param->value : $default;
    }
}
