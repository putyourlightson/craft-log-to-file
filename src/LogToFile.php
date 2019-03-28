<?php

namespace putyourlightson\logtofile;

use Craft;
use craft\helpers\FileHelper;
use yii\base\Component;
use yii\base\ErrorException;

class LogToFile extends Component
{
    // Static Properties
    // =========================================================================

    /**
     * @var string
     */
    public static $handle = '';

    // Static Methods
    // =========================================================================

    /**
     * Logs an info message to a file with the provided handle.
     *
     * @param string $message
     * @param string $handle|null
     */
    public static function info(string $message, string $handle = null)
    {
        self::log($message, $handle, 'info');
    }

    /**
     * Logs an error message to a file with the provided handle.
     *
     * @param string $message
     * @param string $handle|null
     */
    public static function error(string $message, string $handle = null)
    {
        self::log($message, $handle, 'error');
    }

    /**
     * Logs the message to a file with the provided handle and category.
     *
     * @param string $message
     * @param string $handle|null
     * @param string $category
     */
    public static function log(string $message, string $handle = null, string $category = 'info')
    {
        // Default to class handle if none provided
        if (empty($handle)) {
            $handle = self::$handle;
        }

        // Don't continue if handle is still empty
        if (empty($handle)) {
            return;
        }

        $file = Craft::getAlias('@storage/logs/'.$handle.'.log');

        $log = date('Y-m-d H:i:s').' ['.$category.'] '.$message."\n";

        try {
            FileHelper::writeToFile($file, $log, ['append' => true]);
        }
        catch (ErrorException $e) {
        }
    }
}
