<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\GameStartRequest;
use App\Http\Requests\GameEndRequest;
use App\Services\GameService;

/**
 * ゲームコントローラ。
 *
 * @OA\Tag(
 *   name="Games",
 *   description="ゲームAPI",
 * )
 */
class GameController extends Controller
{
    /**
     * @var GameService
     */
    private $service;

    /**
     * サービスをDIしてコントローラを作成する。
     * @param GameService $gameService デッキ関連サービス。
     */
    public function __construct(GameService $gameService)
    {
        $this->service = $gameService;
    }

    /**
     * @OA\Post(
     *   path="/game/start",
     *   summary="ゲーム開始",
     *   description="インゲームを開始する。",
     *   tags={
     *     "Games",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\RequestBody(
     *     description="インゲーム選択情報",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="questId",
     *         type="integer",
     *         description="クエストID",
     *         example=1,
     *       ),
     *       @OA\Property(
     *         property="deckId",
     *         type="integer",
     *         description="使用デッキID",
     *       ),
     *       required={
     *         "questId",
     *         "deckId",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="インゲーム開始情報",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="questlogId",
     *         type="integer",
     *         description="クエスト履歴ID",
     *       ),
     *       @OA\Property(
     *         property="stamina",
     *         type="integer",
     *         description="開始後のスタミナ",
     *       ),
     *       required={
     *         "questlogId",
     *         "stamina",
     *       },
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
    public function start(GameStartRequest $request)
    {
        return $this->service->start(Auth::id(), $request->input());
    }

    /**
     * @OA\Post(
     *   path="/game/end",
     *   summary="ゲーム終了",
     *   description="インゲームを終了する。",
     *   tags={
     *     "Games",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\RequestBody(
     *     description="インゲーム結果",
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="questlogId",
     *         type="integer",
     *         description="クエスト履歴ID",
     *       ),
     *       @OA\Property(
     *         property="status",
     *         type="string",
     *         description="インゲーム結果 (succeed, failed)",
     *         example="succeed",
     *       ),
     *       required={
     *         "questlogId",
     *         "status",
     *       },
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="報酬情報",
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
    public function end(GameEndRequest $request)
    {
        return $this->service->end(Auth::id(), $request->input());
    }
}
