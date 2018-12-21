<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\BadRequestException;
use App\Models\General\User;
use App\Http\Controllers\Controller;

/**
 * ユーザーコントローラ。
 *
 * @OA\Tag(
 *   name="Users",
 *   description="ユーザーAPI",
 * )
 *
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="ユーザーID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="name",
 *     description="ユーザー名",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="email",
 *     description="メールアドレス",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="gameCoin",
 *     description="ゲームコイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="specialCoin",
 *     description="課金コイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="freeSpecialCoin",
 *     description="無償課金コイン",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="level",
 *     description="レベル",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="exp",
 *     description="経験値",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="lastLogin",
 *     description="最終ログイン日時",
 *     type="string",
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
 *     "name",
 *     "email",
 *     "gameCoin",
 *     "specialCoin",
 *     "freeSpecialCoin",
 *     "level",
 *     "exp",
 *     "createdAt",
 *     "updatedAt",
 *   },
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *   path="/users",
     *   summary="ユーザー一覧",
     *   description="ユーザー一覧を取得する。",
     *   tags={
     *     "Users",
     *   },
     *   @OA\Parameter(
     *     in="query",
     *     name="page",
     *     description="ページ番号（先頭ページが1）",
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
     *             @OA\Items(ref="#/components/schemas/User")
     *           ),
     *         ),
     *       }
     *     ),
     *   ),
     * )
     */
    public function index()
    {
        return User::paginate(30);
    }

    /**
     * @OA\Get(
     *   path="/users/{id}",
     *   summary="ユーザー詳細",
     *   description="ユーザーの詳細情報を取得する。",
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
     *       @OA\Property(
     *         property="user",
     *         ref="#/components/schemas/User"
     *       ),
     *       required={
     *         "user",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="取得失敗",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function show($id)
    {
        return ['user' => User::findOrFail($id)];
    }

    /**
     * @OA\Post(
     *   path="/users",
     *   summary="ユーザー登録",
     *   description="ユーザーを登録する。",
     *   tags={
     *     "Users",
     *   },
     *   @OA\RequestBody(
     *     description="パラメータ",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="name",
     *         description="ユーザー名",
     *         type="string",
     *       ),
     *       @OA\Property(
     *         property="email",
     *         description="メールアドレス",
     *         type="string",
     *       ),
     *       @OA\Property(
     *         property="password",
     *         description="パスワード",
     *         type="string",
     *       ),
     *       required={
     *         "name",
     *         "email",
     *         "password",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="user",
     *         ref="#/components/schemas/User"
     *       ),
     *       required={
     *         "user",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="バリデーションNG",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        return ['user' => $user];
    }

    /**
     * @OA\Post(
     *   path="/users/login",
     *   summary="ログイン",
     *   description="ログインする。",
     *   tags={
     *     "Users",
     *   },
     *   @OA\RequestBody(
     *     description="パラメータ",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="email",
     *         description="メールアドレス",
     *         type="string",
     *       ),
     *       @OA\Property(
     *         property="password",
     *         description="パスワード",
     *         type="string",
     *       ),
     *       required={
     *         "email",
     *         "password",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="user",
     *         ref="#/components/schemas/User"
     *       ),
     *       required={
     *         "user",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="バリデーションNG",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            return ['user' => Auth::user()];
        }
        throw new BadRequestException('email or password are incorrect');
    }

    /**
     * @OA\Post(
     *   path="/users/logout",
     *   summary="ログアウト",
     *   description="ログアウトする。",
     *   tags={
     *     "Users",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *   ),
     * )
     */
    public function logout()
    {
        Auth::logout();
    }

    /**
     * @OA\Get(
     *   path="/users/me",
     *   summary="ログイン中ユーザー情報",
     *   description="ログイン中のユーザーの詳細情報を取得する。",
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
     *         property="user",
     *         ref="#/components/schemas/User"
     *       ),
     *       required={
     *         "user",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function me()
    {
        return ['user' => Auth::user()];
    }
}
