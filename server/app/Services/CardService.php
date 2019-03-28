<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Exceptions\NotFoundException;
use App\Models\Globals\User;
use App\Models\Globals\UserCard;

/**
 * カード関連の処理を担うサービスクラス。
 */
class CardService
{
    /**
     * カードを削除する。
     * @param int $userId ユーザーID。
     * @param int $userCardId ユーザーカードID。
     * @return UserCard 削除したカード。
     */
    public function delete(int $userId, int $userCardId) : UserCard
    {
        DB::transaction(function () use ($userId, $userCardId, &$userCard) {
            $userCard = UserCard::lockForUpdate()->where('user_id', $userId)->with('decks')->findOrFail($userCardId);
            // デッキで使用中の場合、削除でデッキが空になるならデッキも消す
            $userCard->decks->load(['deck' => function ($query) {
                return $query->withCount('cards');
            }]);
            foreach ($userCard->decks as $userDeckCard) {
                $userDeck = $userDeckCard->deck;
                $userDeckCard->delete();
                if ($userDeck->cards_count <= 1) {
                    $userDeck->delete();
                }
            }
            $userCard->delete();
        });
        return $userCard;
    }
}
