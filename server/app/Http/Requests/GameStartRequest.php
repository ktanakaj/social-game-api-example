<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Globals\UserQuest;
use App\Models\Masters\Quest;

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
        return [
            'questId' => [
                'required',
                'integer',
                // 公開中のクエストのみ許可する
                Rule::exists('master.quests', 'id')->where(function ($query) {
                    return (new Quest)->scopeActive($query);
                }),
            ],
            'deckId' => 'required|integer|exists:user_decks,id',
        ];
    }

    /**
     * バリデータインスタンスの設定。
     * @param Validator $validator 生成されたバリデータ。
     */
    public function withValidator(Validator $validator) : void
    {
        // 前提クエスト攻略済みもここでチェック
        $validator->after(function ($validator) {
            // 上記のバリデーションに引っかかる場合はそれ以前の問題なので終了
            if (!$validator->errors()->isEmpty()) {
                return;
            }

            $quest = Quest::findOrFail($this->input('questId'));
            if ($quest->previous_id) {
                if (UserQuest::where('user_id', \Auth::id())->where('quest_id', $quest->previous_id)->where('count', '>', 0)->doesntExist()) {
                    // ※ 現在汎用のバリデーションエラーとメッセージに乗っている。分けた方がよいかも？
                    $validator->addFailure('previousId', 'exists:user_quests,quest_id');
                }
            }
        });
    }
}
