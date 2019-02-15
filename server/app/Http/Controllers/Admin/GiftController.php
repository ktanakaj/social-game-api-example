<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\User;
use App\Models\Globals\UserGift;

/**
 * ユーザーギフトコントローラ。
 *
 * @OA\Schema(
 *   schema="UserGiftBody",
 *   type="object",
 *   @OA\Property(
 *     property="text_id",
 *     description="ギフトテキストID",
 *     type="string",
 *     example="GIFT_MESSAGE_COVERING",
 *   ),
 *   @OA\Property(
 *     property="text_options",
 *     type="object",
 *     description="ギフトテキスト追加情報",
 *   ),
 *   @OA\Property(
 *     property="object_type",
 *     description="ギフトオブジェクト種別",
 *     type="string",
 *     example="gameCoin"
 *   ),
 *   @OA\Property(
 *     property="object_id",
 *     description="ギフトオブジェクトID",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="count",
 *     description="個数",
 *     type="number",
 *     example=1,
 *   ),
 *   required={
 *     "text_id",
 *     "object_type",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="UserGift",
 *   type="object",
 *   allOf={
 *     @OA\Schema(ref="#components/schemas/UserGiftBody"),
 *     @OA\Schema(
 *       type="object",
 *       @OA\Property(
 *         property="id",
 *         description="ユーザーギフトID",
 *         type="number",
 *       ),
 *       @OA\Property(
 *         property="user_id",
 *         description="ユーザーID",
 *         type="number",
 *       ),
 *       @OA\Property(
 *         property="created_at",
 *         description="登録日時",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="deleted_at",
 *         description="削除日時",
 *         type="integer",
 *       ),
 *       required={
 *         "id",
 *         "user_id",
 *         "created_at",
 *         "deleted_at",
 *       },
 *     ),
 *   },
 * )
 */
class GiftController extends Controller
{
    /**
     * @OA\Get(
     *   path="/admin/users/{id}/gifts",
     *   summary="ユーザーギフト一覧",
     *   description="ユーザーのギフト一覧を取得する。",
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
     *     description="ギフト一覧",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="ギフト情報配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserGift")
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
        return $user->gifts()->paginate($request->input('max', 20));
    }

    /**
     * @OA\Post(
     *   path="/admin/users/{id}/gifts",
     *   summary="ユーザーギフト付与",
     *   description="ユーザーにギフトを付与する。",
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
     *     description="パラメータ",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/UserGiftBody"),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="付与したギフト",
     *     @OA\JsonContent(ref="#/components/schemas/UserGift"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="バリデーションNG",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="ユーザー取得失敗",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function store(Request $request, User $user)
    {
        // ※ 現状、object_typeやobject_idの有効性まではチェックしていない
        $request->validate([
            'object_type' => 'required|max:32',
            'object_id' => 'integer',
            'count' => 'integer|min:1',
            'text_id' => 'required|exists:master.texts,id',
        ]);
        return $user->gifts()->create($request->input());
    }
}
