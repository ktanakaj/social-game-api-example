<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

/**
 * 管理画面管理者コントローラ。
 *
 * @OA\Tag(
 *   name="Admin",
 *   description="管理画面用API",
 * )
 *
 * @OA\Schema(
 *   schema="AdministratorBody",
 *   type="object",
 *   @OA\Property(
 *     property="email",
 *     description="メールアドレス",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="role",
 *     description="権限",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="note",
 *     description="備考",
 *     type="string",
 *   ),
 *   required={
 *     "email",
 *     "role",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="Administrator",
 *   type="object",
 *   allOf={
 *     @OA\Schema(ref="#components/schemas/AdministratorBody"),
 *     @OA\Schema(
 *       type="object",
 *       @OA\Property(
 *         property="id",
 *         description="管理者ID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="createdAt",
 *         description="登録日時",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="updatedAt",
 *         description="更新日時",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="deletedAt",
 *         description="削除日時",
 *         type="integer",
 *       ),
 *       required={
 *         "id",
 *         "createdAt",
 *         "updatedAt",
 *       },
 *     )
 *   }
 * )
 */
class AdministratorController extends Controller
{
    // TODO: 管理画面を作るには他にもCRUDはじめ各種APIが必要だが、
    //       現状は /admin/~ はAPIの整理用に分けているだけなので、
    //       最小限認証に必要なAPIのみ設置。

    /**
     * @OA\Get(
     *   path="/admin/administrators/me",
     *   summary="認証中の管理者情報",
     *   description="認証中の管理者本人の情報を取得する。",
     *   tags={
     *     "Admin",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="管理者情報",
     *     @OA\JsonContent(ref="#components/schemas/Administrator"),
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
        return Auth::guard('admin')->user();
    }
}
