<?php

namespace App\Http\Controllers;

/**
 * OpenAPI (Swagger) コントローラ。
 *
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="user_model_sandbox API",
 *     version="0.0.1",
 *     description="ゲームのユーザーデータの試験的実装場。"
 *   ),
 *   @OA\Server(
 *     url="/api",
 *   )
 * )
 *
 *
 * @OA\SecurityScheme(
 *   securityScheme="SessionId",
 *   type="apiKey",
 *   in="header",
 *   name="Cookie",
 *   description="セッションID",
 * )
 *
 * @OA\Schema(
 *   schema="Error",
 *   type="object",
 *   @OA\Property(
 *     property="error",
 *     description="エラー情報",
 *     type="object",
 *     @OA\Property(
 *       property="code",
 *       description="エラーコード",
 *       type="string",
 *     ),
 *     @OA\Property(
 *       property="message",
 *       description="エラーメッセージ",
 *       type="string",
 *     ),
 *     @OA\Property(
 *       property="data",
 *       description="追加情報",
 *       type="object",
 *     ),
 *     required={
 *       "code",
 *       "message",
 *     },
 *   ),
 *   required={
 *     "error",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="Pagination",
 *   type="object",
 *   @OA\Property(
 *     property="perPage",
 *     description="1ページの取得件数",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="total",
 *     description="総データ数",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="currentPage",
 *     description="現在ページ番号（先頭ページが1）",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="lastPage",
 *     description="最終ページ番号",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="from",
 *     description="データ開始位置（先頭データが1）",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="to",
 *     description="データ終了位置",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="path",
 *     description="APIパス",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="nextPageUrl",
 *     description="次ページAPIパス",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="prevPageUrl",
 *     description="前ページAPIパス",
 *     type="string",
 *   ),
 *   required={
 *     "data",
 *     "perPage",
 *     "total",
 *     "currentPage",
 *     "lastPage",
 *     "from",
 *     "to",
 *     "path",
 *   },
 * )
 */
class OpenApiController extends Controller
{
    /**
     * OpenAPI定義のJSONを動的に生成する。
     */
    public function __invoke()
    {
        return response(\OpenApi\scan(dirname(__FILE__))->toJson(), 200)->header('Content-Type', 'application/json');
    }
}