<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Models\Masters\MasterModel;

/**
 * マスターコントローラ。
 *
 * @OA\Tag(
 *   name="Masters",
 *   description="マスタAPI",
 * )
 */
class MasterController extends Controller
{
    /**
     * @OA\Get(
     *   path="/masters",
     *   summary="マスタ一覧",
     *   description="マスタ一覧を取得する。",
     *   tags={
     *     "Masters",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="マスタ一覧",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(type="string"),
     *     ),
     *   ),
     * )
     */
    public function index()
    {
        return MasterModel::findTables();
    }

    /**
     * @OA\Get(
     *   path="/masters/{name}",
     *   summary="マスタ取得",
     *   description="公開中のマスタのレコードを取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="name",
     *     description="マスタ名",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="マスタ配列",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         description="マスタ",
     *         type="object",
     *       ),
     *     ),
     *   ),
     * )
     */
    public function findMaster(string $name)
    {
        // TODO: open_at, close_at や enable などの列を見て、公開中のもののみ返す
        // indexの戻り値がテーブル名なので、テーブル名をマスタクラス名に変換して処理
        $classname = '\\App\\Models\\Masters\\' . studly_case(str_singular($name));
        if (!class_exists($classname)) {
            throw new BadRequestException("name={$name} is not found");
        }
        return $classname::all();
    }
}
