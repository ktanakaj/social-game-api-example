<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ゲーム開始API用のフォームリクエスト。
 */
class GameStartRequest extends FormRequest
{
    /**
     * リクエストに適用するバリデーションルールを取得する。
     */
    public function rules() : array
    {
        // TODO: 前提クエスト攻略済みもチェック（サービスでやるかもしれないけど）
        return [
            'questId' => [
                'required',
                'integer',
                // 公開中のクエストのみ許可する
                // TODO: スコープとかに共通化できる？
                Rule::exists('master.quests', 'id')->where(function ($query) {
                    $now = Carbon::now();
                    return $query->where(function ($query) use ($now) {
                        $query->whereNull('open_at')->orWhere('open_at', '<=', $now);
                    })->where(function ($query) use ($now) {
                        $query->whereNull('close_at')->orWhere('close_at', '>=', $now);
                    });
                }),
            ],
            'deckId' => 'required|integer|exists:user_decks,id',
        ];
    }
}
