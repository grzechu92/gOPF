<?php

namespace System;

/**
 * Log class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Log
{
    /**
     * System log.
     *
     * @var int
     */
    const SYSTEM = 0;

    /**
     * Application log.
     *
     * @var int
     */
    const APPLICATION = 1;

    /**
     * An error occured.
     *
     * @var int
     */
    const ERROR = 0;

    /**
     * An notice has been occured.
     *
     * @var int
     */
    const NOTICE = 1;

    /**
     * Don't worry... that is only a information.
     *
     * @var int
     */
    const INFO = 2;

    /**
     * Creates new log entry.
     *
     * @param string $message  Log message
     * @param int    $level    Log level identifier
     * @param int    $location Log location
     */
    public function __construct($message, $level = self::INFO, $location = self::APPLICATION)
    {
        Filesystem::append($this->getPath($location) . date('Y-m-d'), $this->constructLogMessage($message, $level));
    }

    /**
     * Returns location of log file.
     *
     * @param int $location Location code of log file
     *
     * @return string Path to log file
     */
    private function getPath($location)
    {
        switch ($location) {
            case self::SYSTEM:
                return __SYSTEM_PATH . '/log/';
                break;

            case self::APPLICATION:
                return __APPLICATION_PATH . '/log/';
                break;
        }
    }

    /**
     * Constructs the log message.
     *
     * @param string $message Log message
     * @param int    $level   Log level identifier
     *
     * @return string Log entry ready to save
     */
    private function constructLogMessage($message, $level)
    {
        return date('H:i:s') . ' ' . $level . ' ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0') . ' ' . $message . "\n";
    }
}
