<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagingRequest;
use App\Models\Globals\UserCard;

/**
 * カードコントローラ。
 *
 * @OA\Tag(
 *   name="Cards",
 *   description="カードAPI",
 * )
 */
class CardController extends Controller
{
    /**
     * @OA\Get(
     *   path="/cards",
     *   summary="カード一覧",
     *   description="認証中のユーザーのカード一覧を取得する。",
     *   tags={
     *     "Cards",
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
     *     description="カード一覧",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="カード配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserCard")
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
        return UserCard::where('user_id', \Auth::id())->paginate($request->input('max', 20));
    }
}
