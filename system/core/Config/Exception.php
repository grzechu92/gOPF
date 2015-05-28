<?php

namespace System\Config;

/**
 * Config exception class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Exception extends \System\Core\Exception
{
    /**
     * Path to config file.
     *
     * @var string
     */
    public $path;

    /**
     * Overwrites SPL Exception class constructor, allows to define path of file in constructor.
     *
     * @param string $message Exception message
     * @param string $path    Path to config file which cause throwing an exception
     */
    public function __construct($message, $path)
    {
        $this->message = $message;
        $this->path = $path;
    }
}
