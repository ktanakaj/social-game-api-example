<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Masters\Parameter;

/**
 * デッキ作成/更新API用のフォームリクエスト。
 */
class DeckRequest extends FormRequest
{
    /**
     * リクエストに適用するバリデーションルールを取得する。
     */
    public function rules() : array
    {
        $max = Parameter::get('MAX_DECK_POSITION', 9999);
        return [
            '*.userCardId' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('user_cards', 'id')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            '*.position' => "required|integer|min:0|max:{$max}|distinct",
        ];
    }

    /**
     * バリデータインスタンスの設定。
     * @param Validator $validator 生成されたバリデータ。
     */
    public function withValidator(Validator $validator) : void
    {
        // トップレベルが配列のJSONはルールでは指定できない？ようなので、独自にチェック
        $validator->after(function ($validator) {
            $array = $this->input();
            // ※ 現状エラーメッセージは標準のモノを流用。微妙に正しくない
            if (!is_array($array)) {
                $validator->addFailure('array', 'array');
            }
            if (empty($array)) {
                $validator->addFailure('array', 'required');
            }
        });
    }
}
