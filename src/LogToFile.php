<?php

namespace putyourlightson\logtofile;

use Craft;
use craft\helpers\FileHelper;
use yii\base\ErrorException;

class LogToFile
{
    // Static Properties
    // =========================================================================

    /**
     * @var string
     */
    public static $handle = '';

    /**
     * @var bool
     */
    public static $logUserIp = false;

    /**
     * @var bool
     */
    public static $logToCraft = false;

    // Static Methods
    // =========================================================================

    /**
     * Logs an info message to a file with the provided handle.
     *
     * @param string $message
     * @param string|null $handle
     */
    public static function info(string $message, string $handle = null)
    {
        self::log($message, $handle, 'info');
    }

    /**
     * Logs an error message to a file with the provided handle.
     *
     * @param string $message
     * @param string|null $handle
     */
    public static function error(string $message, string $handle = null)
    {
        self::log($message, $handle, 'error');
    }

    /**
     * Logs the message to a file with the provided handle and level.
     *
     * @param string $message
     * @param string|null $handle
     * @param string $level
     */
    public static function log(string $message, string $handle = null, string $level = 'info')
    {
        // Default to class value if none provided
        if ($handle === null) {
            $handle = self::$handle;
        }

        // Don't continue if handle is still empty
        if (empty($handle)) {
            return;
        }

        $file = Craft::getAlias('@storage/logs/'.$handle.'.log');

        // Set user ID
        $userId = '';
        $user = Craft::$app->getUser()->getIdentity();

        if ($user !== null) {
            $userId = $user->id;
        }

        // Set IP address
        $ip = '';

        if (self::$logUserIp) {
            $ip = Craft::$app->getRequest()->getUserIP();
        }

        // Trim message to remove whitespace and empty lines
        $message = trim($message);

        $log = date('Y-m-d H:i:s').' ['.$ip.']['.$userId.']['.$level.'] '.$message."\n";

        try {
            FileHelper::writeToFile($file, $log, ['append' => true]);
        }
        catch (ErrorException $e) {
            Craft::warning('Failed to write log to file `'.$file.'`.');
        }

        if (self::$logToCraft) {
            Craft::getLogger()->log($message, $level, $handle);
        }
    }
}
