<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ゲーム終了API用のフォームリクエスト。
 */
class GameEndRequest extends FormRequest
{
    /**
     * リクエストに適用するバリデーションルールを取得する。
     */
    public function rules() : array
    {
        return [
            'questlogId' => [
                'required',
                'integer',
                Rule::exists('questlogs', 'id')->where(function ($query) {
                    return $query->where('status', 'started');
                }),
            ],
            'status' => [
                'required',
                // TODO: enum化する？
                Rule::in(['succeed', 'failed']),
            ],
        ];
    }
}
