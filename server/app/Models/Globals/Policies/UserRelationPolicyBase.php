<?php

namespace App\Models\Globals\Policies;

use App\Models\Admins\Administrator;
use App\Models\Globals\User;

/**
 * ユーザーの各種関連情報の共通ポリシー用trait。
 *
 * 関連情報のポリシーは基本的に同一なので、ここで一括で定義して、必要に応じて各クラスで上書きさせる。
 */
trait UserRelationPolicyBase
{
    /**
     * ポリシー共通の前処理。
     * @param User|Administrator $user 処理するユーザーまたは管理者。
     * @return bool 処理可能な場合true、不能な場合false、判定しない場合戻り値無し。
     */
    public function before($user, string $ability)
    {
        // 管理者の場合全てOK
        if ($user instanceof Administrator) {
            return true;
        }
    }

    /**
     * ユーザーにより指定された関連情報が更新可能か決める。
     * @param User $user 更新するユーザー。
     * @param mixed $userRelation 更新するユーザーの関連情報。
     * @return bool 更新可能な場合true。
     */
    public function update($user, $userRelation) : bool
    {
        // 自分の関連情報なら更新可能
        return $user->id === $userRelation->user_id;
    }

    /**
     * ユーザーにより指定された関連情報が削除可能か決める。
     * @param User $user 削除するユーザー。
     * @param mixed $userRelation 削除するユーザーの関連情報。
     * @return bool 削除可能な場合true。
     */
    public function delete($user, $userRelation) : bool
    {
        // 自分の関連情報なら削除可能
        return $user->id === $userRelation->user_id;
    }
}
