<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * デッキ作成/更新API用の共通フォームリクエスト。
 */
class DeckRequest extends FormRequest
{
    /**
     * リクエストに適用するバリデーションルールを取得する。
     */
    public function rules() : array
    {
        return [
            '*.userCardId' => 'required|integer|distinct|exists:user_cards,id',
            '*.position' => 'required|integer|min:0|distinct',
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

        // TODO: 最大デッキ数と最大ポジションをparametersマスタ辺りに定義してバリデーションを追加する
    }
}
