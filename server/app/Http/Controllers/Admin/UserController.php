<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\BadRequestException;
use App\Models\Globals\User;
use App\Http\Controllers\Controller;

/**
 * ユーザーコントローラ。
 *
 * @OA\Tag(
 *   name="Admin",
 *   description="管理画面用API",
 * )
 *
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="ユーザーID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="name",
 *     description="ユーザー名",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="email",
 *     description="メールアドレス",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="game_coin",
 *     description="ゲームコイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="special_coin",
 *     description="課金コイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="free_special_coin",
 *     description="無償課金コイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="level",
 *     description="レベル",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="exp",
 *     description="経験値",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="last_login",
 *     description="最終ログイン日時",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     description="登録日時",
 *     type="string",
 *   ),
 *   @OA\Property(
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
     * @OA\Get(
     *   path="/admin/users",
     *   summary="ユーザー一覧",
     *   description="ユーザー一覧を取得する。",
     *   tags={
     *     "Admin",
     *   },
     *   @OA\Parameter(
     *     in="query",
     *     name="page",
     *     description="ページ番号（先頭ページが1）",
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="データ配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
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
     * @OA\Get(
     *   path="/admin/users/{id}",
     *   summary="ユーザー詳細",
     *   description="ユーザーの詳細情報を取得する。",
     *   tags={
     *     "Admin",
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     description="ユーザーID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="user",
     *         ref="#/components/schemas/User"
     *       ),
     *       required={
     *         "user",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="取得失敗",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function show($id)
    {
        return ['user' => User::findOrFail($id)];
    }
}
