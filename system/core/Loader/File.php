<?php

namespace System\Loader;

/**
 * Namespace class for searched file.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class File
{
    /**
     * Class name with namespace.
     *
     * @var string
     */
    private $class;

    /**
     * Class name without namespace.
     *
     * @var string
     */
    private $className;

    /**
     * Class file name.
     *
     * @var string
     */
    private $file;

    /**
     * Class namespace.
     *
     * @var string
     */
    private $namespace;

    /**
     * Exploded namespace.
     *
     * @var string[]
     */
    private $exploded = array();

    /**
     * Initialize file class.
     *
     * @param string $class Class name with namespace
     */
    function __construct($class)
    {
        $class = $this->class = ltrim($class, '\\');
        $separator = strripos($class, '\\');

        $this->namespace = substr($class, 0, $separator);
        $this->className = str_replace('_', DIRECTORY_SEPARATOR, substr($class, $separator + 1));
        $this->file = $this->className . '.php';
        $this->exploded = explode('\\', $this->namespace);
    }

    /**
     * Get class name with namespace.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Get class name without namespace.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Get class file name.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get class namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Get first namespace level.
     *
     * @return string|null First level of namespace
     */
    public function getFirstNamespaceLevel()
    {
        return $this->exploded[0];
    }

    /**
     * Get namespace without first level.
     *
     * @return string Namespace without first level
     */
    public function getNamespaceWithoutFirstLevel()
    {
        if (count($this->exploded) == 1) {
            return '';
        }

        return implode('\\', array_splice($this->exploded, 1));
    }
}