<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\User;
use App\Models\Globals\UserItem;

/**
 * 管理画面アイテムコントローラ。
 *
 * @OA\Schema(
 *   schema="UserItemBody",
 *   type="object",
 *   @OA\Property(
 *     property="itemId",
 *     description="アイテムID",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="count",
 *     description="所持数",
 *     type="number",
 *   ),
 *   required={
 *     "itemId",
 *     "count",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="UserItem",
 *   type="object",
 *   allOf={
 *     @OA\Schema(ref="#components/schemas/UserItemBody"),
 *     @OA\Schema(
 *       type="object",
 *       @OA\Property(
 *         property="id",
 *         description="ユーザーアイテムID",
 *         type="number",
 *       ),
 *       @OA\Property(
 *         property="userId",
 *         description="ユーザーID",
 *         type="number",
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
 *       required={
 *         "id",
 *         "userId",
 *         "createdAt",
 *         "updatedAt",
 *       },
 *     ),
 *   },
 * )
 */
class ItemController extends Controller
{
    /**
     * @OA\Get(
     *   path="/admin/users/{id}/items",
     *   summary="アイテム一覧",
     *   description="ユーザーのアイテム一覧を取得する。",
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
     *       default=20,
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="アイテム一覧",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="アイテム配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserItem")
     *           ),
     *         ),
     *       }
     *     ),
     *   ),
     * )
     */
    public function index(PagingRequest $request, User $user)
    {
        // ※ pageはpaginate内部で勝手に参照される模様
        return $user->items()->paginate($request->input('max', 20));
    }
}
