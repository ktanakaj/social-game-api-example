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
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
