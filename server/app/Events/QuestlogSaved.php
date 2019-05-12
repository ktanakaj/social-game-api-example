<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\Globals\Questlog;

/**
 * ユーザーのクエストプレイ履歴の更新イベント。
 */
class QuestlogSaved
{
    use SerializesModels;

    /**
     * 更新されたクエストプレイ履歴。
     * @var Questlog
     */
    public $log;
    /**
     * 更新前のクエストプレイ履歴。
     * @var array
     */
    public $original;

    /**
     * 新しいイベントインスタンスの生成。
     * @param Questlog $log 更新されたクエストプレイ履歴。
     * @param array $original 更新前のクエストプレイ履歴。
     */
    public function __construct(Questlog $log, array $original)
    {
        $this->log = $log;
        $this->original = $original;
    }
}
