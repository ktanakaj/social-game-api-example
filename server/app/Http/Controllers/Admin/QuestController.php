<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\User;
use App\Models\Globals\UserQuest;

/**
 * 管理画面クエスト情報コントローラ。
 *
 * @OA\Schema(
 *   schema="UserQuestBody",
 *   type="object",
 *   @OA\Property(
 *     property="count",
 *     description="クリア回数",
 *     type="integer",
 *   ),
 *   required={
 *     "count",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="UserQuest",
 *   type="object",
 *   allOf={
 *     @OA\Schema(ref="#components/schemas/UserQuestBody"),
 *     @OA\Schema(
 *       type="object",
 *       @OA\Property(
 *         property="id",
 *         description="ユーザークエストID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="userId",
 *         description="ユーザーID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="questId",
 *         description="クエストID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="createdAt",
 *         description="初回クリア日時",
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
 *         "questId",
 *         "createdAt",
 *         "updatedAt",
 *       },
 *     ),
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="Questlog",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="クエスト履歴ID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="userId",
 *     description="ユーザーID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="questId",
 *     description="クエストID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="status",
 *     description="クエスト状態",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="createdAt",
 *     description="クエスト開始日時",
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
 *     "questId",
 *     "status",
 *     "createdAt",
 *     "updatedAt",
 *   },
 * )
 */
class QuestController extends Controller
{
    /**
     * @OA\Get(
     *   path="/admin/users/{id}/quests",
     *   summary="クエスト一覧",
     *   description="ユーザーのクエスト状況一覧を取得する。",
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
     *     description="クエスト一覧",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="クエスト情報配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserQuest")
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
        return $user->quests()->paginate($request->input('max'));
    }

    /**
     * @OA\Post(
     *   path="/admin/users/{id}/quests",
     *   summary="クエスト達成",
     *   description="ユーザーのクエストを達成済みにする。",
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
     *     description="クエスト情報",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/UserQuestBody"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="questId",
     *             description="クエストID",
     *             type="integer",
     *           ),
     *           required={
     *             "questId",
     *           },
     *         ),
     *       }
     *     ),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="登録したクエスト情報",
     *     @OA\JsonContent(ref="#/components/schemas/UserQuest"),
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
        // ※ storeとdeleteは前クエストIDを考慮していないが、
        //    そもそも管理画面APIだし別にシステム上問題はないので許容する。
        // ※ updateは、現状達成回数に特に意味を想定していないので作らない。
        $request->validate([
            'questId' => 'integer|exists:master.quests,id',
            'count' => 'integer|min:1',
        ]);
        return $user->quests()->updateOrCreate(
            ['quest_id' => $request->input('questId')],
            ['count' => $request->input('count')]
        );
    }

    /**
     * @OA\Delete(
     *   path="/admin/users/{usreId}/quests/{userQuestId}",
     *   summary="クエスト削除",
     *   description="ユーザーのクエスト達成を削除する。",
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
     *     name="userQuestId",
     *     description="ユーザークエストID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="削除されたクエスト情報",
     *     @OA\JsonContent(ref="#components/schemas/UserQuest"),
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
    public function destroy(int $userId, UserQuest $userQuest)
    {
        // 一応ユーザーIDとクエストのIDが一致しているかチェック
        if ($userQuest->user_id !== $userId) {
            throw new NotFoundException('The user quest is not belong to this user');
        }
        $userQuest->delete();
        return $userQuest;
    }

    /**
     * @OA\Get(
     *   path="/admin/users/{id}/quests/logs",
     *   summary="クエスト履歴",
     *   description="ユーザーのクエスト履歴を取得する。",
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
     *     description="クエスト履歴",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="クエスト履歴配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Questlog")
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
    public function logs(PagingRequest $request, User $user)
    {
        // ※ pageはpaginate内部で勝手に参照される模様
        return $user->questlogs()->paginate($request->input('max'));
    }
}
