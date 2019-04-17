<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
     *         property="id",
     *         type="integer",
     *         description="ユーザーID",
     *         example=1,
     *       ),
     *       @OA\Property(
     *         property="token",
     *         type="string",
     *         description="端末トークン",
     *       ),
     *       required={
     *         "id",
     *         "token",
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
            'id' => 'required|integer',
            'token' => 'required',
        ]);
        if (!\Auth::attempt([
            'id' => $request->input('id'),
            'password' => $request->input('token'),
        ])) {
            throw new BadRequestException('id or token is incorrect');
        }
        return \Auth::user();
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
        \Auth::logout();
    }
}
