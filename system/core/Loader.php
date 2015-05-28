<?php

namespace System;

use System\Filesystem;
use System\Loader\NS;

/**
 * Framework libraries loader, based on personalized PSR-0 implementation.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Loader
{
    /**
     * APC internationalized file caching, hidden feature.
     *
     * @var bool
     */
    const APC = __TURBO_MODE;

    /**
     * APC internationalized file caching lifetime, in seconds.
     *
     * @var int
     */
    const APC_LIFETIME = 600;

    /**
     * APC internationalized file caching prefix.
     *
     * @var string
     */
    const APC_PREFIX = 'gOPF-LOADER-';

    /**
     * Reserved namespaces.
     *
     * @var \System\Loader\NS[]
     */
    private static $namespaces = array();

    /**
     * Registers framework loader in PHP loaders registry.
     */
    public function __construct()
    {
        self::registerCoreNamespaces();

        spl_autoload_register(array($this, 'load'));
    }

    /**
     * Register custom reserved namespace.
     *
     * @param NS $ns Reserved namespace data object
     */
    public static function registerReservedNamespace(NS $ns)
    {
        self::$namespaces[$ns->name] = $ns;
    }

    /**
     * Register core reserved namespaces.
     */
    private static function registerCoreNamespaces()
    {
        $reserved = array();

        $reserved[] = new NS('Controllers', __APPLICATION_PATH . DIRECTORY_SEPARATOR . 'controllers');
        $reserved[] = new NS('Repositories', __APPLICATION_PATH . DIRECTORY_SEPARATOR . 'repositories');
        $reserved[] = new NS('Application', __APPLICATION_PATH . DIRECTORY_SEPARATOR . 'classes');
        $reserved[] = new NS('Entities', __APPLICATION_PATH . DIRECTORY_SEPARATOR . 'entities');
        $reserved[] = new NS('Commands', __APPLICATION_PATH . DIRECTORY_SEPARATOR . 'commands');

        foreach ($reserved as $ns) {
            self::registerReservedNamespace($ns);
        }
    }

    /**
     * Loads required class in PSR-0 pattern.
     *
     * @param string $class Class name to load
     *
     * @throws \System\Loader\Exception
     */
    public function load($class)
    {
        if (self::APC) {
            $cache = sha1(self::APC_PREFIX . __ID . $class);

            if ($cached = apc_fetch($cache)) {
                require $cached;

                return;
            }
        }

        $class = ltrim($class, '\\');
        $path = '';

        if ($separator = strripos($class, '\\')) {
            $namespace = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 0, $separator));
            $file = str_replace('_', DIRECTORY_SEPARATOR, substr($class, $separator + 1)) . '.php';
        } else {
            $namespace = '';
            $file = '';
        }

        $exploded = explode('/', $namespace);
        $parsed = substr($namespace, strlen($exploded[0]));

        if ($exploded[0] == 'System') {
            $path = __CORE_PATH . $parsed . DIRECTORY_SEPARATOR . $file;
        }

        if (empty($path)) {
            foreach (self::$namespaces as $ns) {
                if ($exploded[0] == $ns->name) {
                    $path = $ns->build($parsed, $file);
                    break;
                }
            }
        }

        if (empty($path)) {
            $path = __VENDOR_PATH . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR . $file;
        }

        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);

        if (__STAGE == __PRODUCTION || Filesystem::checkFile($path)) {
            if (self::APC) {
                apc_store($cache, $path, self::APC_LIFETIME);
            }

            require $path;
        } else {
            throw new \System\Loader\Exception(\System\I18n::translate('LOADER_UNABLE', array($path)));
        }
    }
}
