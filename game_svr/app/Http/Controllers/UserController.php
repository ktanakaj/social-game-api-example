<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;

/**
 * ユーザーコントローラ。
 */
class UserController extends Controller
{
    /**
     * ユーザーの一覧を表示する。
     * @return Response レスポンス。
     */
    public function index()
    {
        return ['users' => User::all()];
    }

    /**
     * 指定ユーザーの詳細情報を取得する。
     * @param int $id ユーザーID。
     * @return Response レスポンス。
     */
    public function show($id)
    {
        return ['user' => User::findOrFail($id)];
    }
}
