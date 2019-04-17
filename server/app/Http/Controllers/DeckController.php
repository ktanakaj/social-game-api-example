<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeckRequest;
use App\Models\Globals\UserDeck;
use App\Services\DeckService;

/**
 * デッキコントローラ。
 *
 * @OA\Tag(
 *   name="Decks",
 *   description="デッキAPI",
 * )
 *
 * @OA\Schema(
 *   schema="UserDeckBody",
 *   type="array",
 *   @OA\Items(ref="#components/schemas/UserDeckCard")
 * )
 *
 * @OA\Schema(
 *   schema="UserDeckCard",
 *   type="object",
 *   @OA\Property(
 *     property="userCardId",
 *     description="ユーザーカードID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="position",
 *     description="カードの配置",
 *     type="integer",
 *   ),
 *   required={
 *     "userCardId",
 *     "position",
 *   },
 * )
 *
 * @OA\Schema(
 *   schema="UserDeck",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     description="ユーザーデッキID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="userId",
 *     description="ユーザーID",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="no",
 *     description="デッキ番号",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="cards",
 *     description="デッキ内のカード",
 *     type="array",
 *     @OA\Items(ref="#components/schemas/UserDeckCard")
 *   ),
 *   @OA\Property(
 *     property="createdAt",
 *     description="登録日時",
 *     type="integer",
 *   ),
 *   @OA\Property(
 *     property="updatedAt",
 *     description="更新日時",
 *     type="integer",
 *   ),
 *   required={
 *     "id",
 *     "userId",
 *     "no",
 *     "selected",
 *     "cards",
 *     "createdAt",
 *     "updatedAt",
 *   },
 * )
 */
class DeckController extends Controller
{
    /**
     * @var DeckService
     */
    private $service;

    /**
     * サービスをDIしてコントローラを作成する。
     * @param DeckService $deckService デッキ関連サービス。
     */
    public function __construct(DeckService $deckService)
    {
        $this->service = $deckService;
    }

    /**
     * @OA\Get(
     *   path="/decks",
     *   summary="デッキ一覧",
     *   description="認証中のユーザーのデッキ一覧を取得する。",
     *   tags={
     *     "Decks",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="デッキ一覧",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/UserDeck")
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
        return UserDeck::where('user_id', \Auth::id())->with('cards')->get();
    }

    /**
     * @OA\Post(
     *   path="/decks",
     *   summary="デッキ作成",
     *   description="認証中のユーザーにデッキを作成する。",
     *   tags={
     *     "Decks",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\RequestBody(
     *     description="デッキのカード情報",
     *     required=true,
     *     @OA\JsonContent(ref="#components/schemas/UserDeckBody"),
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="作成したデッキ情報",
     *     @OA\JsonContent(ref="#/components/schemas/UserDeck"),
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
    public function store(DeckRequest $request)
    {
        return $this->service->create(\Auth::id(), $request->input());
    }

    /**
     * @OA\Put(
     *   path="/decks/{userDeckId}",
     *   summary="デッキ更新",
     *   description="認証中のユーザーのデッキを更新する。",
     *   tags={
     *     "Decks",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="userDeckId",
     *     description="ユーザーデッキID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\RequestBody(
     *     description="デッキ情報",
     *     required=true,
     *     @OA\JsonContent(ref="#components/schemas/UserDeckBody"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="更新されたデッキ情報",
     *     @OA\JsonContent(ref="#/components/schemas/UserDeck"),
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
    public function update(DeckRequest $request, int $userDeckId)
    {
        return $this->service->update(\Auth::id(), $userDeckId, $request->input());
    }

    /**
     * @OA\Delete(
     *   path="/decks/{userDeckId}",
     *   summary="デッキ削除",
     *   description="認証中のユーザーのデッキを削除する。",
     *   tags={
     *     "Decks",
     *   },
     *   security={
     *     {"SessionId":{}}
     *   },
     *   @OA\Parameter(
     *     in="path",
     *     name="userDeckId",
     *     description="ユーザーデッキID",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="削除されたデッキ情報",
     *     @OA\JsonContent(ref="#components/schemas/UserDeck"),
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
    public function destroy(int $userDeckId)
    {
        return $this->service->delete(\Auth::id(), $userDeckId);
    }
}
