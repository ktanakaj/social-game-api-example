<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Masters\GachaPrice;

/**
 * ガチャ抽選API用のフォームリクエスト。
 */
class GachaRequest extends FormRequest
{
    /**
     * リクエストに適用するバリデーションルールを取得する。
     */
    public function rules() : array
    {
        return [
            'gachaPriceId' => [
                'required',
                'integer',
                Rule::exists('master.gacha_prices', 'id')->where(function ($query) {
                    return (new GachaPrice)->scopeActive($query);
                }),
            ],
            'count' => 'integer|min:1',
        ];
    }
}
