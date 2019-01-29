<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Globals\User;
use App\Models\Globals\UserGift;
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
 *     property="user_id",
 *     description="ユーザーID",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="message_id",
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
 *       property="object_id",
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
 *     property="created_at",
 *     description="登録日時",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     description="更新日時",
 *     type="string",
 *   ),
 *   required={
 *     "id",
 *     "user_id",
 *     "message_id",
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
     *   path="/admin/users/{id}/gifts",
     *   summary="ユーザーギフト一覧",
     *   description="ユーザーのギフト一覧を取得する。",
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
     *   path="/admin/users/{id}/gifts",
     *   summary="ユーザーギフト付与",
     *   description="ユーザーにギフトを付与する。",
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
     *         property="user_gift",
     *         description="付与したギフト",
     *         ref="#/components/schemas/UserGift"
     *       ),
     *       required={
     *         "user_gift",
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

        return ['user_gift' => $userGift];
    }
}
