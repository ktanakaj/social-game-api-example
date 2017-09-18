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
     * イベントマスタの一覧を表示する。
     * @return Response レスポンス。
     */
    public function getEvents()
    {
        return ['masters' => Event::all()];
    }

    /**
     * イベントマスタの一覧を表示する。
     * @return Response レスポンス。
     */
    public function getGiftMessages()
    {
        return ['masters' => GiftMessage::all()];
    }

    /**
     * イベントマスタの一覧を表示する。
     * @return Response レスポンス。
     */
    public function getItems()
    {
        return ['masters' => Item::all()];
    }

    /**
     * イベントマスタの一覧を表示する。
     * @return Response レスポンス。
     */
    public function getItemProperties()
    {
        return ['masters' => ItemProperty::all()];
    }

    /**
     * イベントマスタの一覧を表示する。
     * @return Response レスポンス。
     */
    public function getNews()
    {
        return ['masters' => News::all()];
    }
}
