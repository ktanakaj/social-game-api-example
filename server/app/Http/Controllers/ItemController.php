<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagingRequest;
use App\Models\Globals\UserItem;
use App\Services\ItemService;

/**
 * アイテムコントローラ。
 *
 * @OA\Tag(
 *   name="Items",
 *   description="アイテムAPI",
 * )
 */
class ItemController extends Controller
{
    /**
     * @var ItemService
     */
    private $service;

    /**
     * サービスをDIしてコントローラを作成する。
     * @param ItemService $itemService アイテム関連サービス。
     */
    public function __construct(ItemService $itemService)
    {
        $this->service = $itemService;
    }

    /**
     * @OA\Get(
     *   path="/items",
     *   summary="アイテム一覧",
     *   description="認証中のユーザーのアイテム一覧を取得する。",
     *   tags={
     *     "Items",
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
    public function index(PagingRequest $request)
    {
        // ※ pageはpaginate内部で勝手に参照される模様
        return UserItem::where('user_id', \Auth::id())->notEmpty()->paginate($request->input('max', 20));
    }

    /**
     * @OA\Post(
     *   path="/items/{id}/use",
     *   summary="アイテム使用",
     *   description="認証中のユーザーでアイテムを使用する。",
     *   tags={
     *     "Items",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     description="ユーザーアイテムID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="使用した結果のオブジェクト配列",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ReceivedInfo")
     *     ),
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
    public function use(int $userItemId)
    {
        // TODO: 一度に複数個のアイテムを使用できるようにする（スタミナ回復薬x3とか）。パラメータもいろいろ変わりそう
        return $this->service->use(\Auth::id(), $userItemId);
    }
}
