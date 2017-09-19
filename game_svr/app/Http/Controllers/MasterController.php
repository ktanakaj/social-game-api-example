<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GiftMessage;
use App\Models\Item;
use App\Models\ItemProperty;
use App\Models\News;
use App\Http\Controllers\Controller;

/**
 * マスターコントローラ。
 */
class MasterController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/masters/events",
     *   summary="イベントマスタ取得",
     *   description="イベントマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @SWG\Response(
     *     response=200,
     *     description="成功",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @SWG\Items(
     *           description="イベントマスタ",
     *           type="object",
     *           @SWG\Property(
     *             property="id",
     *             description="イベントID",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="type",
     *             description="イベント種別",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="open_date",
     *             description="イベント開始日時",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="close_date",
     *             description="イベント終了日時",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="title",
     *             description="イベント名",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="created_at",
     *             description="登録日時",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="updated_at",
     *             description="更新日時",
     *             type="string",
     *           ),
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
     * @SWG\Get(
     *   path="/masters/gift_messages",
     *   summary="ギフトメッセージマスタ取得",
     *   description="ギフトメッセージマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @SWG\Response(
     *     response=200,
     *     description="成功",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @SWG\Items(
     *           description="ギフトメッセージマスタ",
     *           type="object",
     *           @SWG\Property(
     *             property="id",
     *             description="ギフトメッセージID",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="message",
     *             description="メッセージ",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="created_at",
     *             description="登録日時",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="updated_at",
     *             description="更新日時",
     *             type="string",
     *           ),
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
     * @SWG\Get(
     *   path="/masters/items",
     *   summary="アイテムマスタ取得",
     *   description="アイテムマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @SWG\Response(
     *     response=200,
     *     description="成功",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @SWG\Items(
     *           description="アイテムマスタ",
     *           type="object",
     *           @SWG\Property(
     *             property="id",
     *             description="アイテムID",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="type",
     *             description="アイテム種別",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="category",
     *             description="アイテムカテゴリ",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="rarity",
     *             description="レアリティ",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="weight",
     *             description="重量",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="name",
     *             description="アイテム名",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="flavor",
     *             description="フレーバーテキスト",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="use_effect",
     *             description="消費効果",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="equipping_effect",
     *             description="装備効果",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="material_effect",
     *             description="素材効果",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="created_at",
     *             description="登録日時",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="updated_at",
     *             description="更新日時",
     *             type="string",
     *           ),
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
     * @SWG\Get(
     *   path="/masters/item_properties",
     *   summary="アイテムプロパティマスタ取得",
     *   description="アイテムプロパティマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @SWG\Response(
     *     response=200,
     *     description="成功",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @SWG\Items(
     *           description="アイテムプロパティマスタ",
     *           type="object",
     *           @SWG\Property(
     *             property="id",
     *             description="アイテムプロパティID",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="type",
     *             description="アイテムプロパティ種別",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="category",
     *             description="アイテムカテゴリ",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="rarity",
     *             description="レアリティ",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="enable",
     *             description="有効無効",
     *             type="boolean",
     *           ),
     *           @SWG\Property(
     *             property="name",
     *             description="アイテムプロパティ名",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="use_effect",
     *             description="消費効果",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="equipping_effect",
     *             description="装備効果",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="material_effect",
     *             description="素材効果",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="created_at",
     *             description="登録日時",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="updated_at",
     *             description="更新日時",
     *             type="string",
     *           ),
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
     * @SWG\Get(
     *   path="/masters/news",
     *   summary="ニュースマスタ取得",
     *   description="ニュースマスタの一覧を取得する",
     *   tags={
     *     "Masters",
     *   },
     *   @SWG\Response(
     *     response=200,
     *     description="成功",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(
     *         property="data",
     *         description="データ配列",
     *         type="array",
     *         @SWG\Items(
     *           description="ニュースマスタ",
     *           type="object",
     *           @SWG\Property(
     *             property="id",
     *             description="ニュースID",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="title",
     *             description="タイトル",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="body",
     *             description="本文",
     *             type="object",
     *           ),
     *           @SWG\Property(
     *             property="open_date",
     *             description="公開開始日時",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="close_date",
     *             description="公開終了日時",
     *             type="number",
     *           ),
     *           @SWG\Property(
     *             property="created_at",
     *             description="登録日時",
     *             type="string",
     *           ),
     *           @SWG\Property(
     *             property="updated_at",
     *             description="更新日時",
     *             type="string",
     *           ),
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
