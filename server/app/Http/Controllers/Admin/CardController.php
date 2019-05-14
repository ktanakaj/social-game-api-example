<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\User;
use App\Models\Globals\UserCard;
use App\Services\CardService;

/**
 * 管理画面カードコントローラ。
 *
 * @OA\Schema(
 *   schema="UserCardBody",
 *   type="object",
 *   @OA\Property(
 *     property="count",
 *     description="同カード重ね合わせ枚数",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="exp",
 *     description="カード経験値",
 *     type="integer",
 *   ),
 * )
 *
 * @OA\Schema(
 *   schema="UserCard",
 *   type="object",
 *   allOf={
 *     @OA\Schema(ref="#components/schemas/UserCardBody"),
 *     @OA\Schema(
 *       type="object",
 *       @OA\Property(
 *         property="id",
 *         description="ユーザーカードID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="userId",
 *         description="ユーザーID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="cardId",
 *         description="カードID",
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
 *       @OA\Property(
 *         property="deletedAt",
 *         description="削除日時",
 *         type="integer",
 *       ),
 *       required={
 *         "id",
 *         "userId",
 *         "cardId",
 *         "createdAt",
 *         "updatedAt",
 *       },
 *     ),
 *   },
 * )
 */
class CardController extends Controller
{
    /**
     * @var CardService
     */
    private $service;

    /**
     * サービスをDIしてコントローラを作成する。
     * @param CardService $cardService カード関連サービス。
     */
    public function __construct(CardService $cardService)
    {
        $this->service = $cardService;
    }

    /**
     * @OA\Get(
     *   path="/admin/users/{id}/cards",
     *   summary="カード一覧",
     *   description="ユーザーのカード一覧を取得する。",
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
    public function index(PagingRequest $request, User $user)
    {
        // ※ pageはpaginate内部で勝手に参照される模様
        return $user->cards()->paginate($request->input('max'));
    }

    /**
     * @OA\Post(
     *   path="/admin/users/{id}/cards",
     *   summary="カード付与",
     *   description="ユーザーにカードを付与する。",
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
     *     description="カード情報",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/UserCardBody"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="cardId",
     *             description="カードID",
     *             type="integer",
     *           ),
     *           required={
     *             "cardId",
     *           },
     *         ),
     *       }
     *     ),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="付与したカード情報",
     *     @OA\JsonContent(ref="#/components/schemas/UserCard"),
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
            'cardId' => 'integer|exists:master.cards,id',
            'count' => 'integer|min:1',
            'exp' => 'integer|min:0',
        ]);
        return $user->cards()->create($request->input());
    }

    /**
     * @OA\Put(
     *   path="/admin/users/{id}/cards/{userCardId}",
     *   summary="カード更新",
     *   description="ユーザーのカードの情報を更新する。",
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
     *     name="userCardId",
     *     description="ユーザーカードID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\RequestBody(
     *     description="カード情報",
     *     required=true,
     *     @OA\JsonContent(ref="#components/schemas/UserCardBody"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="更新されたカード情報",
     *     @OA\JsonContent(ref="#/components/schemas/UserCard"),
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
    public function update(Request $request, int $userId, UserCard $userCard)
    {
        // 一応ユーザーIDとカードのIDが一致しているかチェック
        if ($userCard->user_id !== $userId) {
            throw new NotFoundException('The user card is not belong to this user');
        }
        $request->validate([
            'count' => 'integer|min:1',
            'exp' => 'integer|min:0',
        ]);
        $userCard->fill($request->input());
        $userCard->save();
        return $userCard;
    }

    /**
     * @OA\Delete(
     *   path="/admin/users/{usreId}/cards/{userCardId}",
     *   summary="カード削除",
     *   description="ユーザーのカードを削除する。",
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
     *     name="userCardId",
     *     description="ユーザーカードID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="削除されたカード情報",
     *     @OA\JsonContent(ref="#components/schemas/UserCard"),
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
    public function destroy(int $userId, int $userCardId)
    {
        return $this->service->delete($userId, $userCardId);
    }
}
