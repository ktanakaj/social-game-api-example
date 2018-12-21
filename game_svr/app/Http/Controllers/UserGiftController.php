<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\General\User;
use App\Models\General\UserGift;
use App\Services\UserService;
use App\Http\Controllers\Controller;

/**
 * ユーザーギフトコントローラ。
 *
 * @OA\Schema(
 *   schema="UserGift",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="ユーザーギフトID",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="userId",
 *     description="ユーザーID",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="messageId",
 *     description="ギフトメッセージID",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="data",
 *     type="object",
 *     description="ギフト情報",
 *     @OA\Property(
 *       property="type",
 *       description="ギフト種別",
 *       type="string",
 *     ),
 *     @OA\Property(
 *       property="objectId",
 *       description="ギフトオブジェクトID",
 *       type="number",
 *     ),
 *     @OA\Property(
 *       property="count",
 *       description="個数",
 *       type="number",
 *     ),
 *     required={
 *       "type",
 *       "count",
 *     },
 *   ),
 *   @OA\Property(
 *     property="createdAt",
 *     description="登録日時",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="updatedAt",
 *     description="更新日時",
 *     type="string",
 *   ),
 *   required={
 *     "id",
 *     "userId",
 *     "messageId",
 *     "data",
 *   },
 * )
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
     * @OA\Get(
     *   path="/users/{id}/gifts",
     *   summary="ユーザーギフト一覧",
     *   description="ユーザーのギフト一覧を取得する。",
     *   tags={
     *     "Users",
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     description="ユーザーID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="データ配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserGift")
     *           ),
     *         ),
     *       }
     *     ),
     *   ),
     * )
     */
    public function index($id)
    {
        return UserGift::where('user_id', $id)->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * @OA\Post(
     *   path="/users/{id}/gifts",
     *   summary="ユーザーギフト付与",
     *   description="ユーザーにギフトを付与する。",
     *   tags={
     *     "Users",
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     description="ユーザーID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\RequestBody(
     *     description="パラメータ",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="message_id",
     *         description="ギフトメッセージID",
     *         type="number",
     *       ),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         description="ギフト情報",
     *         @OA\Property(
     *           property="type",
     *           description="ギフト種別",
     *           type="string",
     *         ),
     *         @OA\Property(
     *           property="object_id",
     *           description="ギフトオブジェクトID",
     *           type="number",
     *         ),
     *         @OA\Property(
     *           property="count",
     *           description="個数",
     *           type="number",
     *         ),
     *         required={
     *           "type",
     *           "count",
     *         },
     *       ),
     *       required={
     *         "message_id",
     *         "data",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="userGift",
     *         description="付与したギフト",
     *         ref="#/components/schemas/UserGift"
     *       ),
     *       required={
     *         "userGift",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="バリデーションNG",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="ユーザー取得失敗",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function store(Request $request, $id)
    {
        User::findOrFail($id);

        // ※ 現状、typeやobject_idの有効性まではチェックしていない
        $request->validate([
            'data.type' => 'required|max:32',
            'data.object_id' => 'integer',
            'data.count' => 'required|integer|min:1',
            'message_id' => 'required|integer|exists:master.gift_messages,id',
        ]);

        $userGift = UserGift::create([
            'user_id' => $id,
            'message_id' => $request->input('message_id'),
            'data' => $request->input('data'),
        ]);

        return ['userGift' => $userGift];
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
     *         property="userGift",
     *         description="受け取ったギフト",
     *         ref="#/components/schemas/UserGift"
     *       ),
     *       required={
     *         "userGift",
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
            $result = ['userGift' => $userGift];
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
     *         property="userGifts",
     *         description="受け取ったギフト配列",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/UserGift")
     *       ),
     *       required={
     *         "userGifts",
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
            $result = ['userGifts' => $userGifts];
        });
        return $result;
    }
}
