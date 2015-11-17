<?php

namespace System\Loader;

use Composer\Autoload\ClassLoader;

/**
 * Composer module for loader
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Composer
{
    /**
     * Composer directory path.
     *
     * @var string
     */
    const PATH = __COMPOSER_PATH;

    /**
     * Composer directory name in Composer path.
     *
     * @var string
     */
    const COMPOSER_DIRECTORY = 'composer';

    /**
     * Class loader class name.
     *
     * @var string
     */
    const LOADER = 'ClassLoader.php';

    /**
     * Namespaces file for Composer
     *
     * @var string
     */
    const NAMESPACES_FILE = 'autoload_namespaces.php';

    /**
     * PSR4 file for Composer.
     *
     * @var string
     */
    const PSR4_FILE = 'autoload_psr4.php';

    /**
     * Classmap file for Composer.
     *
     * @var string
     */
    const CLASSMAP_FILE = 'autoload_classmap.php';

    /**
     * Autoload files for Composer.
     *
     * @var string
     */
    const AUTOLOAD_FILE = 'autoload.php';

    /**
     * Is Composer module initialized?
     *
     * @var bool
     */
    private $initialized = false;

    /**
     * Is Composer module installed in framework path?
     *
     * @var bool
     */
    private $installed = false;

    /**
     * Composer class loader module.
     *
     * @var \Composer\Autoload\ClassLoader
     */
    private $loader;

    /**
     * Loader path builder.
     *
     * @var \System\Loader\Load
     */
    private $require;

    /**
     * Initialize Composer loader module.
     *
     * @param \System\Loader\Load $require Library for path building etc.
     */
    function __construct(Load $require)
    {
        $this->require = $require;
        $this->installed = $this->isComposerInstalled();

        if ($this->installed) {
            $files = $this->require->file([self::PATH, self::AUTOLOAD_FILE]);

            foreach ($files as $file) {
                $this->require->file($file);
            }
        }
    }

    /**
     * Find file in composer directory.
     *
     * @param \System\Loader\File $file File to find
     *
     * @return bool|string File path if file is found, false when not
     */
    public function findFile(File $file)
    {
        if (!$this->installed) {
            return false;
        }

        if (!$this->initialized) {
            $this->initialize();
        }

        return $this->loader->findFile($file->getClass());
    }

    /**
     * Initialize Composer class loader.
     */
    private function initialize()
    {
        $this->initialized = true;
        $this->loader = new ClassLoader();

        $load = [
            self::NAMESPACES_FILE => 'set',
            self::PSR4_FILE => 'setPsr4',
            self::CLASSMAP_FILE => 'addClassMap'
        ];

        foreach ($load as $file => $method) {
            $map = $this->require->file([self::PATH, self::COMPOSER_DIRECTORY, $file]);

            if ($file == self::CLASSMAP_FILE) {
                $this->loader->{$method}($map);
            } else {
                foreach ($map as $namespace => $path) {
                    $this->loader->{$method}($namespace, $path);
                }
            }
        }
    }

    /**
     * Is composer installed?
     *
     * @return bool Is installed?
     */
    private function isComposerInstalled()
    {
        return $this->require->exists([self::PATH, self::COMPOSER_DIRECTORY, self::LOADER]);
    }
}