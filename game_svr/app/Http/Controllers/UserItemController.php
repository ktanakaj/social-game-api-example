<?php

namespace App\Http\Controllers;

use App\Models\General\UserItem;
use App\Http\Controllers\Controller;

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
 *     property="userId",
 *     description="ユーザーID",
 *     type="number",
 *   ),
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
 *   @OA\Property(
 *     property="propertyIds",
 *     description="アイテムプロパティID配列",
 *     type="array",
 *     @OA\Items(
 *       description="アイテムプロパティID",
 *       type="number",
 *     ),
 *   ),
 *   @OA\Property(
 *     property="createdAt",
 *     description="登録日時",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="updatedAt",
 *     description="更新日時",
 *     type="string",
 *   ),
 *   required={
 *     "id",
 *     "userId",
 *     "itemId",
 *     "count",
 *     "propertyIds",
 *     "createdAt",
 *     "updatedAt",
 *   },
 * )
 */
class UserItemController extends Controller
{
    /**
     * @OA\Get(
     *   path="/users/{id}/items",
     *   summary="ユーザーアイテム一覧",
     *   description="ユーザーのアイテム一覧を取得する。",
     *   tags={
     *     "Users",
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
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="データ配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserItem")
     *           ),
     *         ),
     *       }
     *     ),
     *   ),
     * )
     */
    public function index($id)
    {
        return UserItem::where('user_id', $id)->orderBy('item_id', 'asc')->paginate(20);
    }
}
