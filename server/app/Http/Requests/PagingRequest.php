<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ページングするAPI用の共通フォームリクエスト。
 */
class PagingRequest extends FormRequest
{
    /**
     * 1ページの最大表示数（継承クラスでの拡張用）。
     * @var int
     */
    const PAGE_MAX = null;

    /**
     * リクエストに適用するバリデーションルールを取得する。
     */
    public function rules() : array
    {
        $rules = [
            'page' => 'integer|min:1',
            'max' => 'integer|min:1',
        ];
        if (!empty(static::PAGE_MAX)) {
            $rules['max'] .= '|max:' . static::PAGE_MAX;
        }
        return $rules;
    }
}
