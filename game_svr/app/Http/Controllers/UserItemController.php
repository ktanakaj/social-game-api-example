<?php

namespace App\Http\Controllers;

use App\Models\UserItem;
use App\Http\Controllers\Controller;

/**
 * ユーザーアイテムコントローラ。
 *
 * @SWG\Definition(
 *   definition="UserItem",
 *   type="object",
 *   @SWG\Property(
 *     property="id",
 *     description="ユーザーアイテムID",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="user_id",
 *     description="ユーザーID",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="item_id",
 *     description="アイテムID",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="count",
 *     description="所持数",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="property_ids",
 *     description="アイテムプロパティID配列",
 *     type="array",
 *     @SWG\Items(
 *       description="アイテムプロパティID",
 *       type="number",
 *     ),
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
 *   },
 * )
 */
class UserItemController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/users/{id}/items",
     *   summary="ユーザーアイテム一覧",
     *   description="ユーザーのアイテム一覧を取得する。",
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
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/UserItem")
     *       ),
     *       required={
     *         "data",
     *       },
     *     ),
     *   ),
     * )
     */
    public function index($id)
    {
        return ['data' => UserItem::where('user_id', $id)->orderBy('item_id', 'asc')->get()];
    }
}
