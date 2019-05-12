<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\User;

/**
 * 管理画面アチーブメントコントローラ。
 *
 * @OA\Schema(
 *   schema="UserAchievement",
 *   @OA\Property(
 *     property="id",
 *     description="ユーザーアチーブメントID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="userId",
 *     description="ユーザーID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="achievementId",
 *     description="アチーブメントID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="score",
 *     description="スコア",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="received",
 *     description="報酬受取済か？",
 *     type="boolean",
 *   ),
 *   @OA\Property(
 *     property="createdAt",
 *     description="登録日時",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="updatedAt",
 *     description="更新日時",
 *     type="integer",
 *   ),
 *   required={
 *     "id",
 *     "userId",
 *     "achievementId",
 *     "score",
 *     "received",
 *     "createdAt",
 *     "updatedAt",
 *   },
 * )
 */
class AchievementController extends Controller
{
    /**
     * @OA\Get(
     *   path="/admin/users/{id}/achievements",
     *   summary="アチーブメント達成状況一覧",
     *   description="ユーザーのアチーブメント達成状況一覧を取得する。",
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
     *     description="アチーブメント達成状況一覧",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="アチーブメント配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserAchievement")
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
        return $user->achievements()->paginate($request->input('max', 20));
    }
}
