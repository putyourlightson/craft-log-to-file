<?php

namespace putyourlightson\logtofile;

use Craft;
use craft\helpers\FileHelper;
use yii\base\ErrorException;
use yii\log\Logger;

class LogToFile
{
    // Constants
    // =========================================================================

    /**
     * Message levels
     * @see https://www.yiiframework.com/doc/api/2.0/yii-log-logger#constants
     */
    const MESSAGE_LEVELS = [
        'error' => Logger::LEVEL_ERROR,
        'info' => Logger::LEVEL_INFO,
        'trace' => Logger::LEVEL_TRACE,
        'profile' => Logger::LEVEL_PROFILE,
        'profileBegin' => Logger::LEVEL_PROFILE_BEGIN,
        'profileEnd' => Logger::LEVEL_PROFILE_END,
    ];

    // Static Properties
    // =========================================================================

    /**
     * @var string
     */
    public static $handle = '';

    /**
     * @var bool
     */
    public static $logToCraft = true;

    /**
     * @var bool
     * @deprecated in 1.1.0
     */
    public static $logUserIp = false;

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

        // Set IP address
        $ip = '';

        if (Craft::$app->getConfig()->getGeneral()->storeUserIps) {
            $ip = Craft::$app->getRequest()->getUserIP();
        }

        // Set user ID
        $userId = '';
        $user = Craft::$app->getUser()->getIdentity();

        if ($user !== null) {
            $userId = $user->id;
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

        // Only log if debug toolbar is not enabled, otherwise this will break it.
        // https://github.com/putyourlightson/craft-blitz/issues/233
        $user = Craft::$app->getUser()->getIdentity();
        $debugToolbarEnabled = $user ? $user->getPreference('enableDebugToolbarForSite') : false;

        if (self::$logToCraft && !$debugToolbarEnabled) {
            // Convert level to a message level that the Yii logger might understand
            $level = self::MESSAGE_LEVELS[$level] ?? $level;

            Craft::getLogger()->log($message, $level, $handle);
        }
    }
}
