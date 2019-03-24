<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Enums\ObjectType;
use App\Models\ObjectReceiver;

/**
 * ギフト付与API用のフォームリクエスト。
 */
class GiftRequest extends FormRequest
{
    /**
     * リクエストに適用するバリデーションルールを取得する。
     */
    public function rules() : array
    {
        return [
            'objectType' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!ObjectReceiver::isSupported($value)) {
                        $fail($attribute.' is not supported.');
                    }
                },
            ],
            'objectId' => 'integer',
            'count' => 'integer|min:1',
            'textId' => 'required|exists:master.texts,id',
        ];
    }

    /**
     * バリデータインスタンスの設定。
     * @param Validator $validator 生成されたバリデータ。
     */
    public function withValidator(Validator $validator) : void
    {
        $validator->sometimes('objectId', 'required|exists:master.items,id', function ($input) {
            return $input->objectType === ObjectType::ITEM;
        });
        $validator->sometimes('objectId', 'required|exists:master.cards,id', function ($input) {
            return $input->objectType === ObjectType::CARD;
        });
    }
}
