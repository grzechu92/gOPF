<?php

namespace System\Terminal\Command;

/**
 * Terminal command: buffer (fills terminal buffer until command processing is aborted).
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Buffer extends \System\Terminal\Command
{
    /**
     * @see \System\Terminal\CommandInterface::help()
     */
    public function help()
    {
        return new \System\Terminal\Help('Does nothing special, just fills buffer with random content');
    }

    /**
     * @see \System\Terminal\CommandInterface::execute()
     */
    public function execute()
    {
        $session = $this->getSession();
        $status = $session->pull();

        while (!$status->abort) {
            $this->buffer(sha1(rand()));
            usleep(100000);

            $status = $session->pull();
        }
    }
}
