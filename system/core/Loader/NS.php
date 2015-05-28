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
     * Namespace target directory.
     *
     * @var string
     */
    public $directory;

    /**
     * Initialzies namespace object.
     *
     * @param string $name      Namespace name
     * @param string $directory Namespace target directory
     */
    public function __construct($name, $directory)
    {
        $this->name = $name;
        $this->directory = $directory;
    }

    /**
     * Build target file path.
     *
     * @param string $namespace Parsed namespace
     * @param string $file      Class filename
     *
     * @return string Target file path
     */
    public function build($namespace, $file)
    {
        return $this->directory . $namespace . DIRECTORY_SEPARATOR . $file;
    }
}
