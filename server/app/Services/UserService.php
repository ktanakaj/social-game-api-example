<?php

namespace App\Services;

use App\Models\Globals\User;
use App\Models\Masters\Parameter;

/**
 * ユーザー関連の処理を担うサービスクラス。
 */
class UserService
{
    /**
     * @var DeckService
     */
    private $deckService;

    /**
     * 入れ子のサービスをDIしてサービスを作成する。
     * @param DeckService $deckService デッキ関連サービス。
     */
    public function __construct(DeckService $deckService)
    {
        $this->deckService = $deckService;
    }

    /**
     * ユーザーを新規作成する。
     * @param string $token 端末トークン。
     * @return User 作成したユーザー。
     */
    public function create(string $token) : User
    {
        \DB::transaction(function () use ($token, &$user) {
            // 新規ユーザーを作成し、マスタで定義された初期データを設定する
            $user = new User();
            if ($data = Parameter::get('INITIAL_USER_DATA')) {
                foreach ($data as $key => $value) {
                    $user->{$key} = $value;
                }
            }
            $user->token = bcrypt($token);
            $user->save();

            $userCards = $user->cards()->createMany(Parameter::get('INITIAL_USER_CARDS', []));
            $user->items()->createMany(Parameter::get('INITIAL_USER_ITEMS', []));

            // カードから初期デッキを作成する
            if ($userCards->isNotEmpty()) {
                $i = 0;
                $this->deckService->create($user->id, $userCards->map(function ($userCard) use (&$i) {
                    return ['user_card_id' => $userCard->id, 'position' => ++$i];
                })->all());
            }
        });
        return $user;
    }
}
