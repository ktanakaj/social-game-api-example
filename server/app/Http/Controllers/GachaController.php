<?php

namespace App\Http\Controllers;

use App\Http\Requests\GachaRequest;
use App\Http\Requests\PagingRequest;
use App\Models\Globals\Gachalog;
use App\Services\GachaService;

/**
 * ガチャコントローラ。
 *
 * @OA\Tag(
 *   name="Gachas",
 *   description="ガチャAPI",
 * )
 *
 * @OA\Schema(
 *   schema="Gacha",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="ガチャID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="nameTextId",
 *     description="ガチャ名テキストID",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="descTextId",
 *     description="ガチャ説明テキストID",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="prices",
 *     description="価格",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/GachaPrice")
 *   ),
 *   required={
 *     "id",
 *     "nameTextId",
 *     "descTextId",
 *     "prices",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="GachaPrice",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="ガチャ価格ID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="gachaId",
 *     description="ガチャID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="objectType",
 *     description="オブジェクト種別",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="objectId",
 *     description="オブジェクトID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="prices",
 *     description="価格",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="times",
 *     description="n連回数",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="openAt",
 *     description="有効期間開始",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="closeAt",
 *     description="有効期間終了",
 *     type="integer",
 *   ),
 *   required={
 *     "id",
 *     "gachaId",
 *     "objectType",
 *     "objectId",
 *     "prices",
 *     "times",
 *     "openAt",
 *     "closeAt",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="GachaDrop",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="ガチャ排出物ID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="gachaId",
 *     description="ガチャID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="objectType",
 *     description="オブジェクト種別",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="objectId",
 *     description="オブジェクトID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="count",
 *     description="個数",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="rate",
 *     description="排出率",
 *     type="number",
 *   ),
 *   @OA\Property(
 *     property="openAt",
 *     description="有効期間開始",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="closeAt",
 *     description="有効期間終了",
 *     type="integer",
 *   ),
 *   required={
 *     "id",
 *     "gachaId",
 *     "objectType",
 *     "objectId",
 *     "count",
 *     "rate",
 *     "openAt",
 *     "closeAt",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="Gachalog",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="ガチャ履歴ID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="userId",
 *     description="ユーザーID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="gachaId",
 *     description="ガチャID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="gachaPriceId",
 *     description="ガチャ価格ID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="drops",
 *     description="排出物",
 *     type="array",
 *     @OA\Items(
 *       type="object",
 *       @OA\Property(
 *         property="objectType",
 *         description="オブジェクト種別",
 *         type="string",
 *       ),
 *       @OA\Property(
 *         property="objectId",
 *         description="オブジェクトID",
 *         type="integer",
 *       ),
 *       @OA\Property(
 *         property="count",
 *         description="個数",
 *         type="integer",
 *       ),
 *       required={
 *         "objectType",
 *         "objectId",
 *         "count",
 *       },
 *     )
 *   ),
 *   @OA\Property(
 *     property="createdAt",
 *     description="ガチャ実施日時",
 *     type="integer",
 *   ),
 *   required={
 *     "id",
 *     "userId",
 *     "gachaId",
 *     "gachaPriceId",
 *     "drops",
 *     "createdAt",
 *   },
 * )
 */
class GachaController extends Controller
{
    /**
     * @var GachaService
     */
    private $service;

    /**
     * サービスをDIしてコントローラを作成する。
     * @param GachaService $gachaService ガチャ関連サービス。
     */
    public function __construct(GachaService $gachaService)
    {
        $this->service = $gachaService;
    }

    /**
     * @OA\Get(
     *   path="/gachas",
     *   summary="ガチャ一覧",
     *   description="認証中のユーザーが利用可能なガチャの一覧を取得する。",
     *   tags={
     *     "Gachas",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="ガチャ一覧",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Gacha")
     *     ),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function index()
    {
        return $this->service->findGachas(\Auth::id());
    }

    /**
     * @OA\Get(
     *   path="/gachas/{id}",
     *   summary="ガチャ詳細",
     *   description="ガチャの確率やラインナップなどを取得する。",
     *   tags={
     *     "Gachas",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     description="ガチャID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="ガチャ詳細",
     *     @OA\JsonContent(
     *       type="object",
     *       allOf={
     *         @OA\Schema(ref="#components/schemas/Gacha"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="drops",
     *             description="ガチャ排出物",
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GachaDrop")
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
    public function show(int $gachaId)
    {
        return $this->service->findGacha($gachaId);
    }

    /**
     * @OA\Post(
     *   path="/gachas",
     *   summary="ガチャ抽選",
     *   description="ガチャを抽選する。",
     *   tags={
     *     "Gachas",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\RequestBody(
     *     description="ガチャ実施情報",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="gachaPriceId",
     *         type="integer",
     *         description="ガチャ価格ID",
     *         example=1,
     *       ),
     *       @OA\Property(
     *         property="count",
     *         type="integer",
     *         description="回数",
     *       ),
     *       required={
     *         "gachaPriceId",
     *         "count",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="抽選結果のオブジェクト配列",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ReceivedInfo")
     *     ),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="バリデーションNG",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="未認証",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="未存在",
     *     @OA\JsonContent(ref="#components/schemas/Error"),
     *   ),
     * )
     */
    public function lot(GachaRequest $request)
    {
        return $this->service->lot(\Auth::id(), $request->input());
    }

    /**
     * @OA\Get(
     *   path="/gachas/logs",
     *   summary="ガチャ履歴",
     *   description="認証中のユーザーのガチャ履歴を取得する。",
     *   tags={
     *     "Gachas",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
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
    public function logs(PagingRequest $request)
    {
        // ※ pageはpaginate内部で勝手に参照される模様
        return Gachalog::where('user_id', \Auth::id())->with('drops')->paginate($request->input('max'));
    }
}
