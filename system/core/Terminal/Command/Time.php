<?php

namespace System\Terminal\Command;

use System\Terminal\Help\Line;

/**
 * Terminal command: time (prints current time).
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Time extends \System\Terminal\Command
{
    /**
     * @see \System\Terminal\CommandInterface::help()
     */
    public function help()
    {
        $lines = array();
        $help = new \System\Terminal\Help('Current server time');

        $lines[] = new Line('time', 'displays current timestamp');
        $lines[] = new Line('time -readable', 'displays current time in readable format');

        $help->addLines($lines);

        return $help;
    }

    /**
     * @see \System\Terminal\CommandInterface::execute()
     */
    public function execute()
    {
        if ($this->getParameter('readable')) {
            $this->buffer(date('H:i:s'));
        } else {
            $this->buffer(time());
        }
    }
}
