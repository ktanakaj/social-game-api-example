<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
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
 *     property="count",
 *     description="所持数",
 *     type="integer",
 *   ),
 *   required={
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
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="userId",
 *         description="ユーザーID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="itemId",
 *         description="アイテムID",
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
 *       required={
 *         "id",
 *         "userId",
 *         "itemId",
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
        return $user->items()->notEmpty()->paginate($request->input('max'));
    }

    /**
     * @OA\Post(
     *   path="/admin/users/{id}/items",
     *   summary="アイテム付与",
     *   description="ユーザーにアイテムを付与または更新する。",
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
     *     description="アイテム情報",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/UserItemBody"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="itemId",
     *             description="アイテムID",
     *             type="integer",
     *           ),
     *           required={
     *             "itemId",
     *           },
     *         ),
     *       }
     *     ),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="付与/更新したアイテム情報",
     *     @OA\JsonContent(ref="#/components/schemas/UserItem"),
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
    public function store(Request $request, User $user)
    {
        $request->validate([
            'itemId' => 'integer|exists:master.items,id',
            'count' => 'integer|min:1',
        ]);
        return $user->items()->updateOrCreate(
            ['item_id' => $request->input('itemId')],
            ['count' => $request->input('count')]
        );
    }

    /**
     * @OA\Put(
     *   path="/admin/users/{id}/items/{userItemId}",
     *   summary="アイテム更新",
     *   description="ユーザーのアイテムの情報を更新する。",
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
     *     in="path",
     *     name="userItemId",
     *     description="ユーザーアイテムID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\RequestBody(
     *     description="アイテム情報",
     *     required=true,
     *     @OA\JsonContent(ref="#components/schemas/UserItemBody"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="更新されたアイテム情報",
     *     @OA\JsonContent(ref="#/components/schemas/UserItem"),
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
    public function update(Request $request, User $user, UserItem $userItem)
    {
        $this->authorizeForUser($user, 'update', $userItem);
        $request->validate([
            'count' => 'integer|min:1',
        ]);
        $userItem->fill($request->input());
        $userItem->save();
        return $userItem;
    }

    /**
     * @OA\Delete(
     *   path="/admin/users/{usreId}/items/{userItemId}",
     *   summary="アイテム削除",
     *   description="ユーザーのアイテムを削除する。",
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
     *     name="userItemId",
     *     description="ユーザーアイテムID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="削除されたアイテム情報",
     *     @OA\JsonContent(ref="#components/schemas/UserItem"),
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
    public function destroy(User $user, UserItem $userItem)
    {
        // レコードは消さず、所持数0にして更新
        $this->authorizeForUser($user, 'delete', $userItem);
        $userItem->count = 0;
        $userItem->save();
        return $userItem;
    }
}
