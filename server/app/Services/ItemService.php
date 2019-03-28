<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Exceptions\NotFoundException;
use App\Models\Globals\UserItem;

/**
 * アイテム関連の処理を担うサービスクラス。
 */
class ItemService
{
    /**
     * 指定されたアイテムを使用する。
     * @param int $userId ユーザーID。
     * @param int $userItemId 使用するアイテムのID。
     * @return array 使用結果のReceivedInfo配列。
     * @throws NotFoundException アイテムがユーザーのものとして存在しない場合。
     */
    public function use(int $userId, int $userItemId) : array
    {
        DB::transaction(function () use ($userId, $userItemId, &$received) {
            $userItem = UserItem::lockForUpdate()->where('user_id', $userId)->findOrFail($userItemId);
            $received = $userItem->use();
            $userItem->save();
        });
        return $received;
    }
}
