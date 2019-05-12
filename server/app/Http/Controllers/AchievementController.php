<?php

namespace App\Http\Controllers;

use App\Services\AchievementService;

/**
 * アチーブメントコントローラ。
 *
 * @OA\Tag(
 *   name="Achievements",
 *   description="アチーブメントAPI",
 * )
 */
class AchievementController extends Controller
{
    /**
     * @var AchievementService
     */
    private $service;

    /**
     * サービスをDIしてコントローラを作成する。
     * @param AchievementService $achievementService アチーブメント関連サービス。
     */
    public function __construct(AchievementService $achievementService)
    {
        $this->service = $achievementService;
    }

    /**
     * @OA\Get(
     *   path="/achievements",
     *   summary="アチーブメント達成状況一覧",
     *   description="認証中のユーザーのアチーブメント達成状況一覧を取得&更新する。",
     *   tags={
     *     "Achievements",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="アチーブメント一覧",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/UserAchievement")
     *     ),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function index()
    {
        return $this->service->findAndUpdate(\Auth::id());
    }

    /**
     * @OA\Post(
     *   path="/achievements/{userAchievementId}/recv",
     *   summary="アチーブメント報酬受取",
     *   description="認証中のユーザーのアチーブメント報酬を受け取る。",
     *   tags={
     *     "Achievements",
     *   },
     *   security={
     *     "SessionId",
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="userAchievementId",
     *     description="ユーザーアチーブメントID",
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
    public function receive(int $userAchievementId)
    {
        return $this->service->receive(\Auth::id(), $userAchievementId);
    }

    /**
     * @OA\Post(
     *   path="/achievements/recv",
     *   summary="全アチーブメント報酬受取",
     *   description="認証中のユーザーの受け取り可能な全アチーブメント報酬を受け取る。",
     *   tags={
     *     "Achievements",
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
        return $this->service->receiveAll(\Auth::id());
    }
}
