<?php

namespace App\Http\Controllers;

/**
 * OpenAPI (Swagger) コントローラ。
 *
 * @OA\Info(
 *   title="laravel-api-example API",
 *   version="0.0.1",
 *   description="Laravel 5勉強用ソシャゲAPIサンプルアプリ。"
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
 *   required={
 *     "data",
 *     "perPage",
 *     "total",
 *     "currentPage",
 *     "lastPage",
 *     "from",
 *     "to",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="ObjectInfo",
 *   type="object",
 *   @OA\Property(
 *     property="type",
 *     description="オブジェクト種別",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="id",
 *     description="オブジェクトのID ※IDを持たない種別の場合null",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="count",
 *     description="オブジェクトの個数",
 *     type="integer",
 *   ),
 *   required={
 *     "type",
 *     "count",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="ReceivedInfo",
 *   type="object",
 *   allOf={
 *     @OA\Schema(ref="#components/schemas/ObjectInfo"),
 *     @OA\Schema(
 *       type="object",
 *       @OA\Property(
 *         property="total",
 *         description="オブジェクトの総所持数",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="isNew",
 *         description="初めて入手したものか？",
 *         type="boolean",
 *       ),
 *       required={
 *         "isNew",
 *       },
 *     )
 *   }
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
