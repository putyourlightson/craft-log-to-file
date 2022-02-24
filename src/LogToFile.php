<?php

namespace putyourlightson\logtofile;

use Craft;
use craft\helpers\FileHelper;
use yii\base\ErrorException;
use yii\db\Exception;
use yii\log\Logger;

class LogToFile
{
    /**
     * Message levels
     * @see https://www.yiiframework.com/doc/api/2.0/yii-log-logger#constants
     */
    public const MESSAGE_LEVELS = [
        'error' => Logger::LEVEL_ERROR,
        'info' => Logger::LEVEL_INFO,
        'trace' => Logger::LEVEL_TRACE,
        'profile' => Logger::LEVEL_PROFILE,
        'profileBegin' => Logger::LEVEL_PROFILE_BEGIN,
        'profileEnd' => Logger::LEVEL_PROFILE_END,
    ];

    /**
     * @var string The default handle to use when writing to a file.
     */
    public static string $handle = '';

    /**
     * @var bool Whether the log to be sent to the Craft logger.
     */
    public static bool $logToCraft = true;

    /**
     * @var bool Whether the logs should be rotated.
     * @since 1.2.0
     */
    public static bool $enableRotation = true;

    /**
     * @var int The maximum file size in KB, before a log file is rotated.
     * @since 1.2.0
     */
    public static int $maxFileSize = 10240;

    /**
     * @var int The maximum number of log files to keep.
     * @since 1.2.0
     */
    public static int $maxLogFiles = 5;

    /**
     * @var bool Whether to rotate logs by copying the file.
     * @since 1.2.0
     */
    public static bool $rotateByCopy = true;

    /**
     * Logs an info message to a file with the provided handle.
     */
    public static function info(string $message, string $handle = null)
    {
        self::log($message, $handle);
    }

    /**
     * Logs an error message to a file with the provided handle.
     */
    public static function error(string $message, string $handle = null)
    {
        self::log($message, $handle, 'error');
    }

    /**
     * Logs the message to a file with the provided handle and level.
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

        // Clear stat cache to ensure getting the real current file size and not a cached one.
        // This may result in rotating twice when cached file size is used on subsequent calls.
        if (self::$enableRotation) {
            clearstatcache();
        }

        $file = Craft::getAlias('@storage/logs/' . $handle . '.log');

        // Set IP address
        $ip = '';

        if (Craft::$app->getConfig()->getGeneral()->storeUserIps && !Craft::$app->getRequest()->isConsoleRequest) {
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

        $log = date('Y-m-d H:i:s') . ' [' . $ip . '][' . $userId . '][' . $level . '] ' . $message . "\n";

        if (self::$enableRotation && @filesize($file) > self::$maxFileSize * 1024) {
            self::rotateFiles($file);
        }

        try {
            FileHelper::writeToFile($file, $log, ['append' => true]);
        }
        // Catch DB exceptions in case the DB cannot be queried for a mutex lock
        catch (ErrorException|Exception) {
            Craft::warning('Failed to write log to file `' . $file . '`.');
        }

        // Only log to Craft if debug toolbar is not enabled, otherwise this will break it
        // https://github.com/putyourlightson/craft-blitz/issues/233
        $user = Craft::$app->getUser()->getIdentity();
        $debugToolbarEnabled = $user ? $user->getPreference('enableDebugToolbarForSite') : false;

        if (self::$logToCraft && !$debugToolbarEnabled) {
            // Convert level to a message level that the Yii logger might understand
            $level = self::MESSAGE_LEVELS[$level] ?? $level;

            Craft::getLogger()->log($message, $level, $handle);
        }
    }

    private static function rotateFiles($file)
    {
        for ($i = self::$maxLogFiles; $i >= 0; --$i) {
            // $i == 0 is the original log file
            $rotateFile = $file . ($i === 0 ? '' : '.' . $i);

            if (is_file($rotateFile)) {
                // Suppress errors because it's possible multiple processes enter into this section.
                if ($i === self::$maxLogFiles) {
                    @unlink($rotateFile);
                    continue;
                }

                $newFile = $file . '.' . ($i + 1);
                self::$rotateByCopy ? self::rotateByCopy($rotateFile, $newFile) : self::rotateByRename($rotateFile, $newFile);

                if ($i === 0) {
                    self::clearLogFile($rotateFile);
                }
            }
        }
    }

    private static function clearLogFile($rotateFile)
    {
        if ($filePointer = @fopen($rotateFile, 'a')) {
            @ftruncate($filePointer, 0);
            @fclose($filePointer);
        }
    }

    private static function rotateByCopy($rotateFile, $newFile)
    {
        @copy($rotateFile, $newFile);
    }

    private static function rotateByRename($rotateFile, $newFile)
    {
        @rename($rotateFile, $newFile);
    }
}
