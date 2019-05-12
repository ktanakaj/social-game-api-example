<?php

namespace App\Services;

use App\Exceptions\BadRequestException;
use App\Models\Globals\User;
use App\Models\Globals\UserDeck;
use App\Models\Masters\Parameter;

/**
 * デッキ関連の処理を担うサービスクラス。
 */
class DeckService
{
    /**
     * 新規デッキを登録する。
     * @param int $userId ユーザーID。
     * @param array $cards カード情報 {userCardId, position} の配列。
     * @return UserDeck 作成したデッキ。
     */
    public function create(int $userId, array $cards) : UserDeck
    {
        \DB::transaction(function () use ($userId, $cards, &$userDeck) {
            // デッキ番号を自動採番して作成
            $user = User::lockForUpdate()->findOrFail($userId);
            $userDeck = new UserDeck();
            $userDeck->no = $this->findNewNo($user);
            if ($userDeck->no > Parameter::get('MAX_DECKS', 9999)) {
                throw new BadRequestException('The user decks are too much');
            }
            $user->decks()->save($userDeck);
            $userDeck->cards = $userDeck->cards()->createMany($cards);

            // 作成したデッキを選択中にする
            $user->last_selected_deck_id = $userDeck->id;
            $user->save();
        });
        return $userDeck;
    }

    /**
     * 新しいデッキ番号を探索する。
     * @param User $user ユーザー情報。
     * @return int 新しいデッキ番号。
     */
    private function findNewNo(User $user) : int
    {
        // ユーザーの全デッキをチェックして、使われていない一番小さな番号を取得する
        // （デッキ削除もあり得るので、単純にMAXではない。）
        $new = 0;
        foreach ($user->decks as $userDeck) {
            if (++$new != $userDeck->no) {
                return $new;
            }
        }
        return ++$new;
    }

    /**
     * デッキを更新する。
     * @param int $userId ユーザーID。
     * @param int $userDeckId ユーザーデッキID。
     * @param array $cards カード情報 {userCardId, position} の配列。
     * @return UserDeck 更新したデッキ。
     */
    public function update(int $userId, int $userDeckId, array $cards) : UserDeck
    {
        \DB::transaction(function () use ($userId, $userDeckId, $cards, &$userDeck) {
            // カード情報を新しく渡されたものに全て置き換える
            $user = User::lockForUpdate()->findOrFail($userId);
            $userDeck = $user->decks()->lockForUpdate()->findOrFail($userDeckId);
            $cardsByPositions = collect($cards)->keyBy('position');
            // ※ 差分更新しようとすると、カードIDが入れ替わるパターンでUNIQUE制約に引っかかる恐れがあるので、
            //    差分があるものは全て一度deleteして再登録
            foreach ($userDeck->cards()->lockForUpdate()->get() as $userDeckCard) {
                $input = $cardsByPositions->get($userDeckCard->position);
                if (!$input) {
                    $userDeckCard->delete();
                    continue;
                }

                $userDeckCard->fill($input);
                if ($userDeckCard->isDirty()) {
                    $userDeckCard->delete();
                } else {
                    $cardsByPositions->forget($userDeckCard->position);
                }
            }
            $userDeck->cards = $userDeck->cards()->createMany($cardsByPositions->values()->all());

            // 更新したデッキを選択中にする
            $user->last_selected_deck_id = $userDeck->id;
            $user->save();
        });
        return $userDeck;
    }

    /**
     * デッキを削除する。
     * @param int $userId ユーザーID。
     * @param int $userDeckId ユーザーデッキID。
     * @return UserDeck 削除したデッキ。
     */
    public function delete(int $userId, int $userDeckId) : UserDeck
    {
        \DB::transaction(function () use ($userId, $userDeckId, &$userDeck) {
            $userDeck = UserDeck::lockForUpdate()->where('user_id', $userId)->with('cards')->findOrFail($userDeckId);
            $userDeck->delete();
        });
        return $userDeck;
    }
}
