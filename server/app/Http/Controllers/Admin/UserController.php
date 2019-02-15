<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\BadRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\User;

/**
 * ユーザーコントローラ。
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
 *     property="game_coins",
 *     description="ゲームコイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="special_coins",
 *     description="課金コイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="free_special_coins",
 *     description="無償課金コイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="exp",
 *     description="経験値",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="stamina",
 *     description="スタミナ",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="stamina_updated_at",
 *     description="スタミナ最終更新日時",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="last_login",
 *     description="最終ログイン日時",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     description="登録日時",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     description="更新日時",
 *     type="integer",
 *   ),
 *   required={
 *     "id",
 *     "name",
 *     "game_coins",
 *     "special_coins",
 *     "free_special_coins",
 *     "exp",
 *     "stamina",
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
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Parameter(
     *     in="query",
     *     name="page",
     *     description="ページ番号（先頭ページが1）",
     *     @OA\Schema(
     *       type="integer",
     *       default=1,
     *     ),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="max",
     *     description="1ページ辺りの取得件数",
     *     @OA\Schema(
     *       type="integer",
     *       default=100,
     *     ),
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
     *             description="ユーザー配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *           ),
     *         ),
     *       }
     *     ),
     *   ),
     * )
     */
    public function index(PagingRequest $request)
    {
        // ※ pageはpaginate内部で勝手に参照される模様
        return User::orderBy('name')->paginate($request->input('max', 100));
    }

    /**
     * @OA\Get(
     *   path="/admin/users/{id}",
     *   summary="ユーザー詳細",
     *   description="ユーザーの詳細情報を取得する。",
     *   tags={
     *     "Admin",
     *   },
     *   security={
     *     {"SessionId":{}}
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
     *     description="ユーザー情報",
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="取得失敗",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function show(User $user)
    {
        return $user;
    }
}
