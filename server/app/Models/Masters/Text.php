<?php

namespace App\Models\Masters;

use App\Models\CamelcaseJson;

/**
 * テキストマスタモデル。
 * ユーザーが見るメッセージはこのマスタで一元管理する。
 * 多言語化もこのマスタで行う。
 */
class Text extends MasterModel
{
    use CamelcaseJson;
}
