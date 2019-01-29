<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\BadRequestException;

/**
 * 認証コントローラ。
 *
 * @OA\Tag(
 *   name="Auth",
 *   description="認証API",
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/login",
     *   summary="ログイン",
     *   description="ログインする。",
     *   tags={
     *     "Auth",
     *   },
     *   @OA\RequestBody(
     *     description="認証情報",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="email",
     *         type="string",
     *         description="メールアドレス",
     *         example="string@example.com",
     *       ),
     *       @OA\Property(
     *         property="password",
     *         type="string",
     *         description="パスワード",
     *       ),
     *       required={
     *         "email",
     *         "password",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="ユーザー情報",
     *     @OA\JsonContent(ref="#components/schemas/User"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="認証失敗",
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
        if (!Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ])) {
            throw new BadRequestException('email or password is incorrect');
        }
        return Auth::user();
    }

    /**
     * @OA\Post(
     *   path="/logout",
     *   summary="ログアウト",
     *   description="ログアウトする。",
     *   tags={
     *     "Auth",
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
}
