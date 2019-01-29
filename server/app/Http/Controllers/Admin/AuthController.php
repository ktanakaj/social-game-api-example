<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\BadRequestException;
use App\Http\Controllers\Controller;

/**
 * 管理画面認証コントローラ。
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/admin/login",
     *   summary="管理者ログイン",
     *   description="管理者を認証する。",
     *   tags={
     *     "Admin",
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
     *         example="admin",
     *       ),
     *       @OA\Property(
     *         property="password",
     *         type="string",
     *         description="パスワード",
     *         example="admin01",
     *       ),
     *       required={
     *         "email",
     *         "password",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="管理者情報",
     *     @OA\JsonContent(ref="#components/schemas/Administrator"),
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
        if (!Auth::guard('admin')->attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ])) {
            throw new BadRequestException('email or password is incorrect');
        }
        return Auth::guard('admin')->user();
    }

    /**
     * @OA\Post(
     *   path="/admin/logout",
     *   summary="管理者ログアウト",
     *   description="管理者の認証状態を終了する。",
     *   tags={
     *     "Admin",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *   ),
     * )
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
    }
}
