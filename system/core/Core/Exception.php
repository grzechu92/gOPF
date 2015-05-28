<?php

namespace System\Core;

/**
 * Core exception class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Exception extends \Exception
{
    /**
     * When Exception is not caught, prints error page with HTTP status defined below.
     *
     * @var int
     */
    public $HTTP = 500;

    /**
     * Returns name of thrown exception.
     *
     * @return string Exception name
     */
    public function getExceptionName()
    {
        return get_class($this);
    }
}
