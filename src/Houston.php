<?php

namespace EdwinLuijten\Houston;

class Houston
{
    /**
     * @var HoustonNotifier
     */
    private static $notifier = null;

    /**
     * @param array $config
     * @param bool $setExceptionHandler
     * @param bool $setErrorHandler
     * @param bool $reportFatalErrors
     */
    public static function init(
        $config = [],
        $setExceptionHandler = true,
        $setErrorHandler = true,
        $reportFatalErrors = true
    ) {

        if (is_null(self::$notifier)) {
            self::$notifier = new HoustonNotifier($config);

            if ($setExceptionHandler) {
                set_exception_handler('EdwinLuijten\\Houston\\Houston::notify');
            }
            if ($setErrorHandler) {
                set_error_handler('EdwinLuijten\\Houston\\Houston::errorHandler');
            }
            if ($reportFatalErrors) {
                register_shutdown_function('EdwinLuijten\\Houston\\Houston::fatalHandler');
            }
        } else {
            self::$notifier->configure($config);
        }
    }

    public static function notifier()
    {
        return self::$notifier;
    }

    public static function notify($exception, $extra = [], $level = null)
    {
        if (is_null(self::$notifier)) {
            return new Response(0, 'Houston we have a problem');
        }

        return self::$notifier->notify($level, $exception, $extra);
    }

    public static function errorHandler($errorNumber, $error, $file, $line)
    {
        if (is_null(self::$notifier)) {
            return;
        }

        $exception = self::createError($errorNumber, $error, $file, $line);

        self::$notifier->notify(null, $exception);
    }

    public static function fatalHandler()
    {
        $lastError = error_get_last();

        if (!is_null($lastError)) {
            $exception = self::createError(
                $lastError['type'],
                $lastError['message'],
                $lastError['file'],
                $lastError['line']
            );

            self::$notifier->notify(null, $exception);
        }
    }

    public static function createError($errorNumber, $error, $file, $line)
    {
        $backtrace = array_slice(debug_backtrace(), 2);

        return new Error($error, $errorNumber, $file, $line, $backtrace);
    }
}