<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\UserGift;
use App\Services\GiftService;

/**
 * ギフトコントローラ。
 *
 * @OA\Tag(
 *   name="Gifts",
 *   description="プレゼントAPI",
 * )
 */
class GiftController extends Controller
{
    /**
     * @var GiftService
     */
    private $service;

    /**
     * サービスをDIしてコントローラを作成する。
     * @param GiftService $giftService プレゼント関連サービス。
     */
    public function __construct(GiftService $giftService)
    {
        $this->service = $giftService;
    }

    /**
     * @OA\Get(
     *   path="/gifts",
     *   summary="ギフト一覧",
     *   description="認証中のユーザーのギフト一覧を取得する。",
     *   tags={
     *     "Gifts",
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
    public function index(PagingRequest $request)
    {
        // ※ pageはpaginate内部で勝手に参照される模様
        return UserGift::where('user_id', Auth::id())->paginate($request->input('max', 20));
    }

    /**
     * @OA\Post(
     *   path="/gifts/{userGiftId}/recv",
     *   summary="ギフト受取",
     *   description="認証中のユーザーのギフトを受け取る。",
     *   tags={
     *     "Gifts",
     *   },
     *   security={
     *     "SessionId",
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="userGiftId",
     *     description="ユーザーギフトID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="受け取ったオブジェクト",
     *     @OA\JsonContent(ref="#/components/schemas/ReceivedInfo"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="取得失敗",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
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
    public function receive(int $userGiftId)
    {
        return $this->service->receive(Auth::id(), $userGiftId);
    }

    /**
     * @OA\Post(
     *   path="/gifts/recv",
     *   summary="全ギフト受取",
     *   description="認証中のユーザーの受け取り可能な全ギフトを受け取る。",
     *   tags={
     *     "Gifts",
     *   },
     *   security={
     *     "SessionId",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="受け取ったオブジェクト配列",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ReceivedInfo")
     *     ),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="取得失敗",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function receiveAll()
    {
        return $this->service->receiveAll(Auth::id());
    }
}
