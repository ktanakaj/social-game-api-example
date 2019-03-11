<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\QuestStatus;

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
                Rule::exists('questlogs', 'id')->where('status', QuestStatus::STARTED),
            ],
            'status' => [
                'required',
                Rule::in([QuestStatus::SUCCEED, QuestStatus::FAILED]),
            ],
        ];
    }
}
