<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * 基底のコントローラクラス。
 *
 * @SWG\Swagger(
 *   @SWG\Info(
 *     title="user_model_sandbox",
 *     description="ゲームのユーザーデータの試験的実装場。",
 *     version="0.0.1",
 *   ),
 *   schemes={
 *     "http",
 *   },
 *   produces={
 *     "application/json",
 *   },
 *   basePath="/api",
 *   @SWG\Tag(
 *     name="Users",
 *     description="ユーザーAPI",
 *   ),
 *   @SWG\Tag(
 *     name="Masters",
 *     description="マスタAPI",
 *   ),
 * )
 * @SWG\Definition(
 *   definition="Pagination",
 *   type="object",
 *   @SWG\Property(
 *     property="data",
 *     description="データ配列",
 *     type="array",
 *   ),
 *   @SWG\Property(
 *     property="per_page",
 *     description="1ページの取得件数",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="total",
 *     description="総データ数",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="current_page",
 *     description="現在ページ番号（先頭ページが1）",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="last_page",
 *     description="最終ページ番号",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="from",
 *     description="データ開始位置（先頭データが1）",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="to",
 *     description="データ終了位置",
 *     type="number",
 *   ),
 *   @SWG\Property(
 *     property="path",
 *     description="APIパス",
 *     type="string",
 *   ),
 *   @SWG\Property(
 *     property="next_page_url",
 *     description="次ページAPIパス",
 *     type="string",
 *   ),
 *   @SWG\Property(
 *     property="prev_page_url",
 *     description="前ページAPIパス",
 *     type="string",
 *   ),
 *   required={
 *     "data",
 *     "per_page",
 *     "total",
 *     "current_page",
 *     "last_page",
 *     "from",
 *     "to",
 *     "path",
 *   },
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
