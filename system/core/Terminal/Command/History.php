<?php

namespace System\Terminal\Command;

use System\Terminal;

/**
 * Terminal command: history (shows terminal commands history).
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class History extends \System\Terminal\Command
{
    /**
     * @see \System\Terminal\CommandInterface::help()
     */
    public function help()
    {
        $help = new \System\Terminal\Help('Commands history');

        return $help;
    }

    /**
     * @see \System\Terminal\CommandInterface::execute()
     */
    public function execute()
    {
        $session = $this->getSession();
        $status = $session->pull();

        $history = array();
        foreach ($status->history as $command) {
            $history[] = $command;
        }

        $session->buffer(implode("\n", $history));
    }
}
