<?php

namespace App\Http\Controllers;

/**
 * OpenAPI (Swagger) コントローラ。
 *
 * @OA\Info(
 *   title="user-model-sandbox API",
 *   version="0.0.1",
 *   description="ゲームのユーザーデータの試験的実装場。"
 * ),
 * @OA\Server(
 *   url="/",
 * )
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
 *   schema="IntBoolean",
 *   type="integer",
 *   enum={0, 1}
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
 *     property="per_page",
 *     description="1ページの取得件数",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="total",
 *     description="総データ数",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="current_page",
 *     description="現在ページ番号（先頭ページが1）",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="last_page",
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
 *   required={
 *     "data",
 *     "per_page",
 *     "total",
 *     "current_page",
 *     "last_page",
 *     "from",
 *     "to",
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
        // 動作している環境に応じて、動的に基準となるパスを書き換える
        $docs = \OpenApi\scan(dirname(__FILE__));
        $docs->servers[0]->url = preg_replace("/\/api-docs\.json.*$/", '/', $_SERVER['REQUEST_URI']);
        return response($docs->toJson(), 200)->header('Content-Type', 'application/json');
    }
}
