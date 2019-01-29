<?php

namespace App\Http\Controllers;

use App\Models\Masters\Event;
use App\Models\Masters\GiftMessage;
use App\Models\Masters\Item;
use App\Models\Masters\ItemProperty;
use App\Models\Masters\News;

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
     *   path="/masters/events",
     *   summary="イベントマスタ取得",
     *   description="イベントマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @OA\Items(
     *           description="イベントマスタ",
     *           type="object",
     *           @OA\Property(
     *             property="id",
     *             description="イベントID",
     *             type="number",
     *           ),
     *           @OA\Property(
     *             property="type",
     *             description="イベント種別",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="open_date",
     *             description="イベント開始日時",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="close_date",
     *             description="イベント終了日時",
     *             type="number",
     *           ),
     *           @OA\Property(
     *             property="title",
     *             description="イベント名",
     *             type="object",
     *           ),
     *           required={
     *             "id",
     *             "type",
     *             "open_date",
     *             "close_date",
     *             "title",
     *           },
     *         ),
     *       ),
     *       required={
     *         "data",
     *       },
     *     ),
     *   ),
     * )
     */
    public function getEvents()
    {
        return ['data' => Event::all()];
    }

    /**
     * @OA\Get(
     *   path="/masters/gift-messages",
     *   summary="ギフトメッセージマスタ取得",
     *   description="ギフトメッセージマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @OA\Items(
     *           description="ギフトメッセージマスタ",
     *           type="object",
     *           @OA\Property(
     *             property="id",
     *             description="ギフトメッセージID",
     *             type="number",
     *           ),
     *           @OA\Property(
     *             property="message",
     *             description="メッセージ",
     *             type="object",
     *           ),
     *           required={
     *             "id",
     *             "message",
     *           },
     *         ),
     *       ),
     *       required={
     *         "data",
     *       },
     *     ),
     *   ),
     * )
     */
    public function getGiftMessages()
    {
        return ['data' => GiftMessage::all()];
    }

    /**
     * @OA\Get(
     *   path="/masters/items",
     *   summary="アイテムマスタ取得",
     *   description="アイテムマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @OA\Items(
     *           description="アイテムマスタ",
     *           type="object",
     *           @OA\Property(
     *             property="id",
     *             description="アイテムID",
     *             type="number",
     *           ),
     *           @OA\Property(
     *             property="type",
     *             description="アイテム種別",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="category",
     *             description="アイテムカテゴリ",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="rarity",
     *             description="レアリティ",
     *             type="number",
     *           ),
     *           @OA\Property(
     *             property="weight",
     *             description="重量",
     *             type="number",
     *           ),
     *           @OA\Property(
     *             property="name",
     *             description="アイテム名",
     *             type="object",
     *           ),
     *           @OA\Property(
     *             property="flavor",
     *             description="フレーバーテキスト",
     *             type="object",
     *           ),
     *           @OA\Property(
     *             property="use_effect",
     *             description="消費効果",
     *             type="object",
     *           ),
     *           @OA\Property(
     *             property="equipping_effect",
     *             description="装備効果",
     *             type="object",
     *           ),
     *           @OA\Property(
     *             property="material_effect",
     *             description="素材効果",
     *             type="object",
     *           ),
     *           required={
     *             "id",
     *             "type",
     *             "category",
     *             "rarity",
     *             "weight",
     *             "name",
     *             "flavor",
     *             "use_effect",
     *             "equipping_effect",
     *             "material_effect",
     *           },
     *         ),
     *       ),
     *       required={
     *         "data",
     *       },
     *     ),
     *   ),
     * )
     */
    public function getItems()
    {
        return ['data' => Item::all()];
    }

    /**
     * @OA\Get(
     *   path="/masters/item-properties",
     *   summary="アイテムプロパティマスタ取得",
     *   description="アイテムプロパティマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @OA\Items(
     *           description="アイテムプロパティマスタ",
     *           type="object",
     *           @OA\Property(
     *             property="id",
     *             description="アイテムプロパティID",
     *             type="number",
     *           ),
     *           @OA\Property(
     *             property="type",
     *             description="アイテムプロパティ種別",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="category",
     *             description="アイテムカテゴリ",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="rarity",
     *             description="レアリティ",
     *             type="number",
     *           ),
     *           @OA\Property(
     *             property="enable",
     *             description="有効無効",
     *             type="boolean",
     *           ),
     *           @OA\Property(
     *             property="name",
     *             description="アイテムプロパティ名",
     *             type="object",
     *           ),
     *           @OA\Property(
     *             property="use_effect",
     *             description="消費効果",
     *             type="object",
     *           ),
     *           @OA\Property(
     *             property="equipping_effect",
     *             description="装備効果",
     *             type="object",
     *           ),
     *           @OA\Property(
     *             property="material_effect",
     *             description="素材効果",
     *             type="object",
     *           ),
     *           required={
     *             "id",
     *             "type",
     *             "category",
     *             "rarity",
     *             "enable",
     *             "name",
     *             "use_effect",
     *             "equipping_effect",
     *             "material_effect",
     *           },
     *         ),
     *       ),
     *       required={
     *         "data",
     *       },
     *     ),
     *   ),
     * )
     */
    public function getItemProperties()
    {
        return ['data' => ItemProperty::all()];
    }

    /**
     * @OA\Get(
     *   path="/masters/news",
     *   summary="ニュースマスタ取得",
     *   description="ニュースマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @OA\Response(
     *     response=200,
     *     description="成功",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @OA\Items(
     *           description="ニュースマスタ",
     *           type="object",
     *           @OA\Property(
     *             property="id",
     *             description="ニュースID",
     *             type="number",
     *           ),
     *           @OA\Property(
     *             property="title",
     *             description="タイトル",
     *             type="object",
     *           ),
     *           @OA\Property(
     *             property="body",
     *             description="本文",
     *             type="object",
     *           ),
     *           @OA\Property(
     *             property="open_date",
     *             description="公開開始日時",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="close_date",
     *             description="公開終了日時",
     *             type="number",
     *           ),
     *           required={
     *             "id",
     *             "title",
     *             "body",
     *             "open_date",
     *             "close_date",
     *           },
     *         ),
     *       ),
     *       required={
     *         "data",
     *       },
     *     ),
     *   ),
     * )
     */
    public function getNews()
    {
        return ['data' => News::all()];
    }
}
