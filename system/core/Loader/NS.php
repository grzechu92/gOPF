<?php

namespace System\Loader;

/**
 * Namespace class for class loader.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class NS
{
    /**
     * Namespace name.
     *
     * @var string
     */
    public $name;

    /**
     * Namespace target directory path parts.
     *
     * @var string[]
     */
    public $directory = array();

    /**
     * Initialzies namespace object.
     *
     * @param string   $name      Namespace name
     * @param string[] $directory Namespace target directory parts
     */
    public function __construct($name, $directory)
    {
        $this->name = $name;
        $this->directory = $directory;
    }

    /**
     * Build target file path.
     *
     * @param \System\Loader\File $file File to build
     *
     * @return string[] Target file path parts
     */
    public function build($file)
    {
        return array_merge($this->directory, [$file->getNamespaceWithoutFirstLevel(), $file->getFile()]);
    }

    /**
     * Check if file is matching to namespace?
     *
     * @param \System\Loader\File $file File to check
     *
     * @return bool Is file matching namespace?
     */
    public function match(File $file)
    {
        return $this->name == $file->getFirstNamespaceLevel();
    }
}
