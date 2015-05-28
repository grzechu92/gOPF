<?php

namespace System\Dispatcher;

/**
 * Filesystem exception class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Exception extends \System\Core\Exception
{
    /**
     * Overwrites SPL Exception class constructor, allows to define HTTP error code in exception.
     *
     * @param string $message Exception message
     * @param int    $code    HTTP Error Code
     */
    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->HTTP = $code;
    }
}
