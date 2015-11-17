<?php

namespace System\Loader;

class Cache
{
    /**
     * File caching lifetime, in seconds.
     *
     * @var int
     */
    const LIFETIME = 600;

    /**
     * Classes cache container prefix.
     *
     * @var string
     */
    const CLASSES_PREFIX = 'gOPF-LOADER-CLASSES-';

    const AUTOLOAD_PREFIX = 'gOPF-LOADER-AUTOLOAD-';

    private $enabled = false;
    private $classes = array();
    private $autoload = array();

    public function __construct()
    {
        $this->enabled = function_exists('apc_store');

        if ($this->enabled) {
            $this->classes = apc_fetch(self::CLASSES_PREFIX . __ID);
            $this->autoload = apc_fetch(self::AUTOLOAD_PREFIX . __ID);
        }
    }

    public function __destruct() {
        if ($this->enabled) {
            apc_store(self::CLASSES_PREFIX . __ID, $this->classes, self::LIFETIME);
        }
    }

    public function getClassPath($class) {
        if (!$this->enabled || !isset($this->classes[$class])) {
            return '';
        }

        return $this->classes[$class];
    }

    public function setClassPath($class, $path) {
        $this->classes[$class] = $path;
    }

    public function getAutoloadFiles() {
        return $this->autoload;
    }

    public function addAutoloadFile($file) {
        $this->autoload[] = $file;
    }
}