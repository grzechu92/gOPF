<?php

namespace System\Loader;

/**
 * File loader and path builder for Loader.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Load
{
    /**
     * Load file from path parts or from path if is known.
     *
     * @param string[]|string $path Path parts or path
     *
     * @return mixed File content if is returning something
     */
    public function file($path) {
        if (is_array($path)) {
            return $this->load($this->build($path));
        } else {
            return $this->load($path);
        }
    }

    public function exists($parts = array())
    {
        return is_file($this->build($parts));
    }

    /**
     * Require selected file.
     *
     * @param string $path Path to required file
     *
     * @return mixed File content if is returning something
     */
    public function load($path)
    {
        return require $path;
    }

    /**
     * Build path from path parts.
     *
     * @param string[] $parts Path parts
     *
     * @return string Build paths
     */
    public function build($parts = array())
    {
        if (count($parts) == 0) {
            return '';
        }

        return str_replace('\\', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $parts));
    }
}
