<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     *         property="token",
     *         description="端末トークン",
     *         type="string",
     *       ),
     *       required={
     *         "token",
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
        // ユーザーを新規登録して認証済みにする
        $request->validate([
            'token' => 'required|max:191',
        ]);

        $user = new User();
        $user->token = bcrypt($request->input('token'));
        $user->save();

        Auth::login($user);
        return $user;
    }

    /**
     * @OA\Get(
     *   path="/users/me",
     *   summary="ユーザー情報",
     *   description="認証中のユーザーの情報を取得する。",
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
