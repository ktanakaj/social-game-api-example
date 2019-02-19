<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\User;

/**
 * 管理画面ユーザーコントローラ。
 *
 * @OA\Schema(
 *   schema="UserBody",
 *   type="object",
 *   @OA\Property(
 *     property="name",
 *     description="ユーザー名",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="gameCoins",
 *     description="ゲームコイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="specialCoins",
 *     description="課金コイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="freeSpecialCoins",
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
 * )
 *
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   allOf={
 *     @OA\Schema(ref="#components/schemas/UserBody"),
 *     @OA\Schema(
 *       type="object",
 *       @OA\Property(
 *         property="id",
 *         description="ユーザーID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="lastLogin",
 *         description="最終ログイン日時",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="createdAt",
 *         description="登録日時",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="updatedAt",
 *         description="更新日時",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="deletedAt",
 *         description="削除日時",
 *         type="integer",
 *       ),
 *       required={
 *         "id",
 *         "name",
 *         "gameCoins",
 *         "specialCoins",
 *         "freeSpecialCoins",
 *         "exp",
 *         "stamina",
 *         "createdAt",
 *         "updatedAt",
 *       },
 *     ),
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
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
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
     *   summary="ユーザー情報",
     *   description="ユーザーの情報を取得する。",
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
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="未存在",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * @OA\Put(
     *   path="/admin/users/{id}",
     *   summary="ユーザー更新",
     *   description="ユーザーの情報を更新する。",
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
     *   @OA\RequestBody(
     *     description="ユーザー情報",
     *     required=true,
     *     @OA\JsonContent(ref="#components/schemas/UserBody"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="更新されたユーザー情報",
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="権限無し",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="未存在",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'gameCoins' => 'integer|min:0',
            'specialCoins' => 'integer|min:0',
            'freeSpecialCoins' => 'integer|min:0',
            'exp' => 'integer|min:0',
            'stamina' => 'integer|min:0',
        ]);
        $user->fill($request->input());
        $user->save();
        return $user;
    }
}
