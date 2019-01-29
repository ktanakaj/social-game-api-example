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
     *         example="string@example.com",
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
     *     response=201,
     *     description="登録したユーザー情報",
     *     @OA\JsonContent(ref="#components/schemas/User"),
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
        // ユーザーを新規登録してログイン済みにする
        $request->validate([
            'name' => 'required|max:191',
            'email' => 'required|max:191|email|unique:users',
            'password' => 'required',
        ]);

        $user = new User($request->input());
        $user->password = bcrypt($request->input('password'));
        $user->save();

        Auth::login($user);
        return $user;
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
     *     description="ユーザー情報",
     *     @OA\JsonContent(ref="#components/schemas/User"),
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
        return Auth::user();
    }
}
