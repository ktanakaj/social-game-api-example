<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Globals\User;
use App\Models\Globals\UserGift;
use App\Services\UserService;

/**
 * ユーザーギフトコントローラ。
 *
 * @OA\Schema(
 *   schema="ReceivedObject",
 *   type="object",
 *   @OA\Property(
 *     property="object_type",
 *     description="受け取ったものの種別",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="object_id",
 *     description="受け取ったもののID ※IDを持たない種別の場合null",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="count",
 *     description="受け取った件数",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="total",
 *     description="受け取った後の件数",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="is_new",
 *     description="初めて獲得したものか？",
 *     type="boolean",
 *   ),
 *   required={
 *     "object_type",
 *     "count",
 *     "is_new",
 *   },
 * )
 */
class GiftController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * サービスをDIしてコントローラを作成する。
     * @param UserService $userService ユーザー関連サービス。
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *   path="/gifts/{userGiftId}/recv",
     *   summary="ユーザーギフト受取",
     *   description="ユーザーのギフトを受け取る。",
     *   tags={
     *     "Users",
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
     *     @OA\JsonContent(ref="#/components/schemas/ReceivedObject"),
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
     *     description="ギフトが存在しない",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function receive(Request $request, int $userGiftId)
    {
        $userId = Auth::id();
        DB::transaction(function () use ($userId, $userGiftId, &$result) {
            $userGift = UserGift::lockForUpdate()->findOrFail($userGiftId);
            if ($userGift->user_id != $userId) {
                throw new \InvalidArgumentException("The user gift is not belong to me");
            }
            $result = $userGift->receive();
        });
        return $result;
    }

    /**
     * @OA\Post(
     *   path="/gifts/recv",
     *   summary="全ユーザーギフト受取",
     *   description="ユーザーの全ギフトを受け取る。",
     *   tags={
     *     "Users",
     *   },
     *   security={
     *     "SessionId",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="受け取ったオブジェクト配列",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ReceivedObject")
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
    public function allReceive(Request $request)
    {
        $userId = Auth::id();
        DB::transaction(function () use ($userId, &$results) {
            $results = [];
            $userGifts = UserGift::lockForUpdate()->where(['user_id' => $userId])->get();
            foreach ($userGifts as $userGift) {
                $results[] = $userGift->receive();
            }
        });
        return $results;
    }
}
