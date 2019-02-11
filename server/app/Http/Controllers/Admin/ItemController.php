<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Globals\User;
use App\Models\Globals\UserItem;

/**
 * ユーザーアイテムコントローラ。
 *
 * @OA\Schema(
 *   schema="UserItem",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="ユーザーアイテムID",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="user_id",
 *     description="ユーザーID",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="item_id",
 *     description="アイテムID",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="count",
 *     description="所持数",
 *     type="number",
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
 *     "user_id",
 *     "item_id",
 *     "count",
 *     "created_at",
 *     "updated_at",
 *   },
 * )
 */
class ItemController extends Controller
{
    /**
     * @OA\Get(
     *   path="/admin/users/{id}/items",
     *   summary="ユーザーアイテム一覧",
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
    public function index(User $user)
    {
        return $user->items()->paginate(20);
    }
}
