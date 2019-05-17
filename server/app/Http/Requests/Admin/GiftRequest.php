<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Enums\ObjectType;
use App\Models\Masters\Card;
use App\Models\Masters\Item;
use App\Models\Masters\Text;
use App\Models\ObjectReceiver;
use App\Rules\ExistsByEloquent;

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
            'textId' => ['required', new ExistsByEloquent(Text::class)],
        ];
    }

    /**
     * バリデータインスタンスの設定。
     * @param Validator $validator 生成されたバリデータ。
     */
    public function withValidator(Validator $validator) : void
    {
        // オブジェクト種別に応じてマスタIDをチェックする
        $validator->sometimes('objectId', ['required', new ExistsByEloquent(Item::class)], $this->checkObjectType(ObjectType::ITEM));
        $validator->sometimes('objectId', ['required', new ExistsByEloquent(Card::class)], $this->checkObjectType(ObjectType::CARD));
    }

    /**
     * オブジェクト種別をチェックするクロージャを生成する。
     * @param int $type オブジェクト種別。
     * @return \Closure チェック用のクロージャ。
     */
    private function checkObjectType(string $type) : \Closure
    {
        return function ($input) use ($type) {
            return $input->objectType === $type;
        };
    }
}
