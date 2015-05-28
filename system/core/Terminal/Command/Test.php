<?php

namespace System\Terminal\Command;

use System\Terminal\Help;

/**
 * Terminal command: test (print test command parameters).
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Test extends \System\Terminal\Command
{
    /**
     * @see \System\Terminal\CommandInterface::help()
     */
    public function help()
    {
        $help = new Help(Help::INTERNAL);

        return $help;
    }

    /**
     * @see \System\Terminal\CommandInterface::execute()
     */
    public function execute()
    {
        $this->buffer(print_r($this, true));
    }
}
