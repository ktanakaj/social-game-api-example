<?php

namespace App\Http\Controllers;

use App\Models\UserItem;
use App\Http\Controllers\Controller;

/**
 * ユーザーアイテムコントローラ。
 */
class UserItemController extends Controller
{
    /**
     * ユーザーのアイテム一覧を表示する。
     * @param int $id ユーザーID。
     * @return Response レスポンス。
     */
    public function index($id)
    {
        return ['userItems' => UserItem::where('user_id', $id)->orderBy('item_id', 'asc')->get()];
    }
}
