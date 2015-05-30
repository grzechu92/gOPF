<?php

namespace System;

use System\Filesystem;
use System\Loader\NS;
use System\Loader\File;

/**
 * Framework class loader.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Loader
{
    /**
     * File caching lifetime, in seconds.
     *
     * @var int
     */
    const CACHE_LIFETIME = 600;

    /**
     * File cache container prefix.
     *
     * @var string
     */
    const CACHE_PREFIX = 'gOPF-LOADER-';

    /**
     * Is cache enabled?
     *
     * @var bool
     */
    private $cache = false;

    /**
     * Reserved namespaces.
     *
     * @var \System\Loader\NS[]
     */
    private static $namespaces = array();

    /**
     * Class name to path container.
     *
     * @var string[]
     */
    private static $cached = array();

    /**
     * Registers framework loader in PHP loaders registry.
     */
    public function __construct()
    {
        $this->cache = function_exists('apc_store');

        self::$namespaces[] = new NS('Controllers', [__APPLICATION_PATH, 'controllers']);
        self::$namespaces[] = new NS('Repositories', [__APPLICATION_PATH, 'repositories']);
        self::$namespaces[] = new NS('Application', [__APPLICATION_PATH, 'classes']);
        self::$namespaces[] = new NS('Entities', [__APPLICATION_PATH, 'entities']);
        self::$namespaces[] = new NS('Commands', [__APPLICATION_PATH, 'commands']);

        if ($this->cache) {
            self::$cached = apc_fetch(self::CACHE_PREFIX . __ID);
        }

        spl_autoload_register([$this, 'load']);
    }

    /**
     * Save cached data if any
     */
    public function __destruct() {
        if ($this->cache && count(self::$cached) > 0) {
            apc_store(self::CACHE_PREFIX . __ID, self::$cached, self::CACHE_LIFETIME);
        }
    }

    /**
     * Register custom reserved namespace.
     *
     * @param NS $ns Reserved namespace data object
     */
    public static function registerReservedNamespace(NS $ns)
    {
        self::$namespaces[] = $ns;
    }

    /**
     * Loads required class.
     *
     * @param string $class Class name to load
     *
     * @throws \System\Loader\Exception
     */
    public function load($class)
    {
        if (isset(self::$cached[$class])) {
            $this->requirePath(self::$cached[$class]);
            return;
        }

        $file = new File($class);

        $parts = $this->findFile($file);
        $path = $this->buildPath($parts);

        if (__STAGE == __PRODUCTION || is_file($path)) {
            if ($this->cache) {
                self::$cached[$class] = $path;
            }

            $this->requirePath($path);
        } else {
            throw new \System\Loader\Exception(\System\I18n::translate('LOADER_UNABLE', array($path)));
        }
    }

    /**
     * Require selected file.
     *
     * @param string $path Path to required file
     */
    private function requirePath($path)
    {
        require $path;
    }

    /**
     * Find file in framework filesystem.
     *
     * @param \System\Loader\File $file File to find
     *
     * @return string[] Path parts to merge
     */
    private function findFile(File $file)
    {
        if ($file->getFirstNamespaceLevel() == 'System') {
            $namespace = $file->getNamespaceWithoutFirstLevel();

            if ($namespace == '') {
                return [__CORE_PATH, $file->getFile()];
            } else {
                return [__CORE_PATH, $namespace, $file->getFile()];
            }
        }

        foreach (self::$namespaces as $ns) {
            if ($ns->match($file)) {
                return $ns->build($file);
            }
        }
    }

    /**
     * Build path from path parts.
     *
     * @param string[] $path Path parts
     *
     * @return string Build paths
     */
    private function buildPath($path = array())
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $path));
    }
}
