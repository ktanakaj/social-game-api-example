<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\BadRequestException;
use App\Models\Globals\User;

/**
 * ユーザーコントローラ。
 *
 * @OA\Tag(
 *   name="Users",
 *   description="ユーザーAPI",
 * )
 */
class UserController extends Controller
{
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
