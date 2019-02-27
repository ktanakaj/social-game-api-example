<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiftRequest;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\User;
use App\Models\Globals\UserGift;

/**
 * 管理画面ギフトコントローラ。
 *
 * @OA\Schema(
 *   schema="UserGiftBody",
 *   type="object",
 *   @OA\Property(
 *     property="textId",
 *     description="ギフトテキストID",
 *     type="string",
 *     example="GIFT_MESSAGE_COVERING",
 *   ),
 *   @OA\Property(
 *     property="textOptions",
 *     type="object",
 *     description="ギフトテキスト追加情報",
 *   ),
 *   @OA\Property(
 *     property="objectType",
 *     description="ギフトオブジェクト種別",
 *     type="string",
 *     example="gameCoin"
 *   ),
 *   @OA\Property(
 *     property="objectId",
 *     description="ギフトオブジェクトID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="count",
 *     description="個数",
 *     type="integer",
 *     example=1,
 *   ),
 *   required={
 *     "textId",
 *     "objectType",
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
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="userId",
 *         description="ユーザーID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="createdAt",
 *         description="登録日時",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="deletedAt",
 *         description="削除日時",
 *         type="integer",
 *       ),
 *       required={
 *         "id",
 *         "userId",
 *         "createdAt",
 *         "deletedAt",
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
     *   summary="ギフト一覧",
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
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
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
     *   summary="ギフト付与",
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
     *     description="ギフト情報",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/UserGiftBody"),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="付与したギフト情報",
     *     @OA\JsonContent(ref="#/components/schemas/UserGift"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="バリデーションNG",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
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
    public function store(GiftRequest $request, User $user)
    {
        return $user->gifts()->create($request->input());
    }

    /**
     * @OA\Delete(
     *   path="/admin/users/{usreId}/gifts/{userGiftId}",
     *   summary="ギフト削除",
     *   description="ユーザーのギフトを削除する。",
     *   tags={
     *     "Admin",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="usreId",
     *     description="ユーザーID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="path",
     *     name="userGiftId",
     *     description="ユーザーギフトID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="削除されたギフト情報",
     *     @OA\JsonContent(ref="#components/schemas/UserGift"),
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
    public function destroy(int $userId, UserGift $userGift)
    {
        // 一応ユーザーIDとギフトのIDが一致しているかチェック
        if ($userGift->user_id !== $userId) {
            throw new NotFoundException('The user gift is not belong to this user');
        }
        $userGift->delete();
        return $userGift;
    }
}
