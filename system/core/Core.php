<?php

namespace System;

/**
 * Holds all framework modules in place.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 *
 * @property \System\Dispatcher                  $dispatcher
 * @property \System\Dispatcher\ContextInterface $context
 * @property \System\User                        $user
 */
class Core extends Singleton
{
    /**
     * gOPF Core version number.
     *
     * @var string
     */
    const VERSION = '2.0.0 RC';

    /**
     * gOPF Core build time.
     *
     * @var string
     */
    const BUILD = '150602090137';

    /**
     * gOPF Core stage (__DEVELOPMENT or __PRODUCTION).
     *
     * @var int
     */
    const STAGE = __STAGE;

    /**
     * Unique User ID.
     *
     * @var string
     */
    public static $UUID;

    /**
     * Loaded core modules.
     *
     * @var object[]
     */
    private $modules = array();

    /**
     * Creates instance of Core class, and loads UUID (Unique User ID).
     */
    protected function __construct()
    {
        $UUID = isset($_COOKIE['__UUID']) ? $_COOKIE['__UUID'] : false;

        if (!$UUID || !preg_match('#([0-9a-f]{40})#', $UUID)) {
            self::$UUID = self::generateUUID();
            self::setUUID();
        } else {
            self::$UUID = $UUID;
        }
    }

    /**
     * Get core module.
     *
     * @param string $name Module name
     *
     * @return object Module
     */
    public function __get($name)
    {
        if (!isset($this->modules[$name])) {
            $class = '\\System\\' . ucfirst($name);
            $this->modules[$name] = new $class();
        }

        return $this->modules[$name];
    }

    /**
     * Set core module.
     *
     * @param string $name  Module name
     * @param object $value Module
     */
    public function __set($name, $value)
    {
        $this->modules[$name] = $value;
    }

    /**
     * Drop client UUID.
     */
    public static function resetUUID()
    {
        self::$UUID = self::generateUUID();
        self::setUUID();
    }

    /**
     * Generates new UUID.
     *
     * @return string Generated UUID
     */
    private static function generateUUID()
    {
        return sha1('gOPF-UUID' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '') . rand(1, 1000000));
    }

    /**
     * Set UUID.
     */
    private static function setUUID()
    {
        setcookie('__UUID', self::$UUID, time() + 24 * 3600, '/');
    }

    /**
     * Starts request processing.
     */
    public function run()
    {
        Request::instance();
        Router::instance();

        $this->dispatcher->dispatch();
    }
}
