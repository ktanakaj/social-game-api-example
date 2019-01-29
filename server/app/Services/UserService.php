<?php

namespace App\Services;

use App\Models\Globals\User;
use App\Models\Globals\UserItem;

/**
 * ユーザー関連の処理を担うサービスクラス。
 */
class UserService
{
    /**
     * ユーザーにオブジェクト種別からオブジェクトを追加する。
     * @param int $userId ユーザーID。
     * @param array $dataArray オブジェクト情報配列。
     */
    public function addObjects(int $userId, array $dataArray) : void
    {
        // TODO: 複数個同時に受け取った時の効率化とかロックとか
        $user = User::lockForUpdate()->findOrFail($userId);
        foreach ($dataArray as $data) {
            if ($user->addObjectByType($data)) {
                continue;
            } elseif (UserItem::addObjectByType($userId, $data)) {
                continue;
            } else {
                throw new \InvalidArgumentException("The type '${data['type']}' is undefined");
            }
        }
        $user->save();
    }

    /**
     * ユーザーにオブジェクト種別からオブジェクトを追加する。
     * @param int $userId ユーザーID。
     * @param array $dataArray オブジェクト情報。
     */
    public function addObject(int $userId, array $data) : void
    {
        $this->addObjects($userId, [$data]);
    }
}
