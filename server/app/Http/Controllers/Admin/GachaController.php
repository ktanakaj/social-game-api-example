<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\User;

/**
 * 管理画面ガチャ情報コントローラ。
 */
class GachaController extends Controller
{
    /**
     * @OA\Get(
     *   path="/admin/users/{id}/gachas/logs",
     *   summary="ガチャ履歴",
     *   description="ユーザーのガチャ履歴を取得する。",
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
     *   @OA\Parameter(
     *     in="query",
     *     name="page",
     *     description="ページ番号（先頭ページが1）",
     *     @OA\Schema(
     *       type="integer",
     *       default=1,
     *     ),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="max",
     *     description="1ページ辺りの取得件数",
     *     @OA\Schema(
     *       type="integer",
     *       default=20,
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="ガチャ履歴",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Pagination"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="ガチャ履歴配列",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Gachalog")
     *           ),
     *         ),
     *       }
     *     ),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function logs(PagingRequest $request, User $user)
    {
        // ※ pageはpaginate内部で勝手に参照される模様
        return $user->gachalogs()->with('drops')->paginate($request->input('max', 20));
    }
}
