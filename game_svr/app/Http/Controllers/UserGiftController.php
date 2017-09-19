<?php

namespace App\Http\Controllers;

use App\Models\UserGift;
use App\Http\Controllers\Controller;

/**
 * ユーザーギフトコントローラ。
 *
 * @SWG\Definition(
 *   definition="UserGift",
 *   type="object",
 *   @SWG\Property(
 *     property="id",
 *     description="ユーザーギフトID",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="user_id",
 *     description="ユーザーID",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="type",
 *     description="ギフト種別",
 *     type="string",
 *   ),
 *   @SWG\Property(
 *     property="gift_id",
 *     description="ギフトID",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="count",
 *     description="個数",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="message_id",
 *     description="ギフトメッセージID",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="created_at",
 *     description="登録日時",
 *     type="string",
 *   ),
 *   @SWG\Property(
 *     property="deleted_at",
 *     description="更新日時",
 *     type="string",
 *   ),
 *   required={
 *     "id",
 *     "user_id",
 *     "type",
 *     "count",
 *     "message_id",
 *   },
 * )
 */
class UserGiftController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/users/{id}/gifts",
     *   summary="ユーザーギフト一覧",
     *   description="ユーザーのギフト一覧を取得する。",
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
     *         @SWG\Items(ref="#/definitions/UserGift")
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
        return ['data' => UserGift::where('user_id', $id)->orderBy('created_at', 'desc')->get()];
    }
}
