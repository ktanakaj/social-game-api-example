<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

    /**
     * @SWG\Post(
     *   path="/users/{id}/gifts",
     *   summary="ユーザーギフト付与",
     *   description="ユーザーにギフトを付与する。",
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
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="パラメータ",
     *     required=true,
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="type",
     *         description="ギフト種別",
     *         type="string",
     *       ),
     *       @SWG\Property(
     *         property="gift_id",
     *         description="ギフトID",
     *         type="number",
     *       ),
     *       @SWG\Property(
     *         property="count",
     *         description="個数",
     *         type="number",
     *       ),
     *       @SWG\Property(
     *         property="message_id",
     *         description="ギフトメッセージID",
     *         type="number",
     *       ),
     *     ),
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="成功",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="userGift",
     *         ref="#/definitions/UserGift"
     *       ),
     *       required={
     *         "userGift",
     *       },
     *     ),
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="バリデーションNG",
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="ユーザー取得失敗",
     *   ),
     * )
     */
    public function store(Request $request, $id)
    {
        User::findOrFail($id);

        // TODO: type, gift_id の有効値チェックする
        $request->validate([
            'type' => 'required|max:32',
            'gift_id' => 'integer',
            'count' => 'required|integer|min:1',
            'message_id' => 'required|integer|exists:gift_messages,id',
        ]);

        $userGift = new UserGift();
        $userGift->user_id = $id;
        $userGift->type = $request->input('type');
        $userGift->count = $request->input('count');
        $userGift->message_id = $request->input('message_id');
        $giftId = $request->input('gift_id');
        if ($giftId !== null && $giftId !== '') {
            $userGift->gift_id = $giftId;
        }
        $userGift->save();

        return ['userGift' => $userGift];
    }
}
