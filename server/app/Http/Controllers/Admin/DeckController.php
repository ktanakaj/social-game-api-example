<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Globals\User;

/**
 * 管理画面デッキコントローラ。
 */
class DeckController extends Controller
{
    /**
     * @OA\Get(
     *   path="/admin/users/{id}/decks",
     *   summary="デッキ一覧",
     *   description="ユーザーのデッキ一覧を取得する。",
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
     *     description="デッキ一覧",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/UserDeck")
     *     ),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function index(User $user)
    {
        return $user->decks()->with('cards')->get();
    }
}
