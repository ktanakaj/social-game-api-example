<?php

namespace App\Http\Controllers;

use App\Models\UserGift;
use App\Http\Controllers\Controller;

/**
 * ユーザーギフトコントローラ。
 */
class UserGiftController extends Controller
{
    /**
     * ユーザーのギフト一覧を表示する。
     * @param int $id ユーザーID。
     * @return Response レスポンス。
     */
    public function index($id)
    {
        return ['userItems' => UserGift::where('user_id', $id)->orderBy('created_at', 'desc')->get()];
    }
}
