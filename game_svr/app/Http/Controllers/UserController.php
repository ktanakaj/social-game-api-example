<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;

/**
 * ユーザーコントローラ。
 *
 * @SWG\Definition(
 *   definition="User",
 *   type="object",
 *   @SWG\Property(
 *     property="id",
 *     description="ユーザーID",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="name",
 *     description="ユーザー名",
 *     type="string",
 *   ),
 *   @SWG\Property(
 *     property="email",
 *     description="メールアドレス",
 *     type="string",
 *   ),
 *   @SWG\Property(
 *     property="game_coin",
 *     description="ゲームコイン",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="special_coin",
 *     description="課金コイン",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="free_special_coin",
 *     description="無償課金コイン",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="level",
 *     description="レベル",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="exp",
 *     description="経験値",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="last_login",
 *     description="最終ログイン日時",
 *     type="string",
 *   ),
 *   @SWG\Property(
 *     property="created_at",
 *     description="登録日時",
 *     type="string",
 *   ),
 *   @SWG\Property(
 *     property="updated_at",
 *     description="更新日時",
 *     type="string",
 *   ),
 *   required={
 *     "id",
 *     "name",
 *     "email",
 *     "game_coin",
 *     "special_coin",
 *     "free_special_coin",
 *     "level",
 *     "exp",
 *     "created_at",
 *     "updated_at",
 *   },
 * )
 */
class UserController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/users",
     *   summary="ユーザー一覧",
     *   description="ユーザー一覧を取得する。",
     *   tags={
     *     "Users",
     *   },
     *   @SWG\Parameter(
     *     in="query",
     *     name="page",
     *     type="number",
     *     description="ページ番号（先頭ページが1）",
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="成功",
     *     @SWG\Schema(
     *       type="object",
     *       allOf={
     *         @SWG\Schema(ref="#definitions/Pagination"),
     *         @SWG\Schema(
     *           type="object",
     *           @SWG\Property(
     *             property="data",
     *             description="データ配列",
     *             type="array",
     *             @SWG\Items(ref="#/definitions/User")
     *           ),
     *         ),
     *       }
     *     ),
     *   ),
     * )
     */
    public function index()
    {
        return User::paginate(30);
    }

    /**
     * @SWG\Get(
     *   path="/users/{id}",
     *   summary="ユーザー詳細",
     *   description="ユーザーの詳細情報を取得する。",
     *   tags={
     *     "Users",
     *   },
     *   @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     type="number",
     *     description="ユーザーID",
     *     required=true,
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="成功",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="user",
     *         ref="#/definitions/User"
     *       ),
     *       required={
     *         "user",
     *       },
     *     ),
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="取得失敗",
     *   ),
     * )
     */
    public function show($id)
    {
        return ['user' => User::findOrFail($id)];
    }

    /**
     * @SWG\Post(
     *   path="/users",
     *   summary="ユーザー登録",
     *   description="ユーザーを登録する。",
     *   tags={
     *     "Users",
     *   },
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="パラメータ",
     *     required=true,
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="name",
     *         description="ユーザー名",
     *         type="string",
     *       ),
     *       @SWG\Property(
     *         property="email",
     *         description="メールアドレス",
     *         type="string",
     *       ),
     *       @SWG\Property(
     *         property="password",
     *         description="パスワード",
     *         type="string",
     *       ),
     *       required={
     *         "name",
     *         "email",
     *         "password",
     *       },
     *     ),
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="成功",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="user",
     *         ref="#/definitions/User"
     *       ),
     *       required={
     *         "user",
     *       },
     *     ),
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="バリデーションNG",
     *   ),
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        return ['user' => $user];
    }
}
