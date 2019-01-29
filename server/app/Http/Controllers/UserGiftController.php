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
 */
class UserGiftController extends Controller
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
     *   path="/users/me/gifts/{userGiftId}/recv",
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
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="user_gift",
     *         description="受け取ったギフト",
     *         ref="#/components/schemas/UserGift"
     *       ),
     *       required={
     *         "user_gift",
     *       },
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
     *   @OA\Response(
     *     response=404,
     *     description="ギフトが存在しない",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function receive(Request $request, $userGiftId)
    {
        $userId = Auth::id();
        DB::transaction(function () use ($userId, $userGiftId, &$result) {
            $userGift = UserGift::lockForUpdate()->findOrFail($userGiftId);
            if ($userGift->user_id != $userId) {
                throw new \InvalidArgumentException("The user gift is not belong to me");
            }
            $this->userService->addObject($userGift->user_id, $userGift->data);
            $userGift->delete();
            $result = ['user_gift' => $userGift];
        });
        return $result;
    }

    /**
     * @OA\Post(
     *   path="/users/me/gifts/recv",
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
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="user_gifts",
     *         description="受け取ったギフト配列",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/UserGift")
     *       ),
     *       required={
     *         "user_gifts",
     *       },
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
        DB::transaction(function () use ($userId, &$result) {
            $userGifts = UserGift::lockForUpdate()->where(['user_id' => $userId])->get();
            $this->userService->addObjects($userId, $userGifts->map(function ($v) {
                return $v->data;
            })->all());
            foreach ($userGifts as $userGift) {
                $userGift->delete();
            }
            $result = ['user_gifts' => $userGifts];
        });
        return $result;
    }
}
