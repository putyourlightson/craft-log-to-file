<?php

namespace putyourlightson\logtofile;

use Craft;
use craft\web\Application as CraftWebApp;
use craft\console\Application as CraftConsoleApp;
use putyourlightson\elementstatusevents\commands\ScheduledElements;
use yii\base\Application as YiiApp;
use yii\base\BootstrapInterface;
use yii\base\Component;
use craft\base\Element;
use craft\events\ElementEvent;
use craft\services\Elements;
use putyourlightson\elementstatusevents\behaviors\ElementStatusBehavior;
use yii\base\Event;
use yii\caching\CacheInterface;

class LogToFile extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Logs the message.
     *
     * @param string $message
     * @param string $handle
     */
    public static function log(string $message, string $handle)
    {
        $file = Craft::getAlias('@storage/logs/'.$handle.'.log');
        $log = date('Y-m-d H:i:s').' '.$message."\n";

        \craft\helpers\FileHelper::writeToFile($file, $log, ['append' => true]);
    }
}
