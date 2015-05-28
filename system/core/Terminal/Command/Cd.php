<?php

namespace System\Terminal\Command;

use System\Terminal\Exception;
use System\Terminal\Status;

/**
 * Terminal command: cd (changes current terminal directory).
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Cd extends \System\Terminal\Command
{
    /**
     * @see \System\Terminal\CommandInterface::help()
     */
    public function help()
    {
        $help = new \System\Terminal\Help('Change directory');
        $help->add(new \System\Terminal\Help\Line('cd [path]', 'change directory to pointed path'));

        return $help;
    }

    /**
     * @see \System\Terminal\CommandInterface::execute()
     */
    public function execute()
    {
        $session = $this->getSession();
        $status = $session->pull();

        if (empty($this->getValue())) {
            $this->buffer(__ROOT_PATH . $status->path);

            return;
        }

        if ($this->getValue() == '..') {
            $status = $this->moveUp($status);
        } elseif ($this->getValue()[0] == '/') {
            $status = $this->absoluteMoveTo($status);
        } else {
            $status = $this->relativeMoveTo($status);
        }

        $session->push($status);
    }

    /**
     * Moves relatively path in, relate to current path.
     *
     * @param \System\Terminal\Status $status Terminal status
     *
     * @throws \System\Terminal\Exception
     *
     * @return \System\Terminal\Status Updated terminal status
     */
    private function relativeMoveTo(Status $status)
    {
        $path = $status->path . $this->getValue();

        if ($this->checkDirectory($path)) {
            $status->path = $this->buildPath($path);

            return $status;
        } else {
            throw new Exception('Wrong way!');
        }
    }

    /**
     * Moves absolutely path, in relate to __ROOT_PATH.
     *
     * @param \System\Terminal\Status $status Terminal status
     *
     * @throws \System\Terminal\Exception
     *
     * @return \System\Terminal\Status Updated terminal status
     */
    private function absoluteMoveTo(Status $status)
    {
        if ($this->checkDirectory($this->getValue())) {
            $status->path = $this->buildPath($this->getValue());

            return $status;
        } else {
            throw new Exception('Wrong way!');
        }
    }

    /**
     * Goes up in directories tree, in relate to __ROOT_PATH.
     *
     * @param \System\Terminal\Status $status Terminal status
     *
     * @throws \System\Terminal\Exception
     *
     * @return \System\Terminal\Status Updated terminal status
     */
    private function moveUp(Status $status)
    {
        if ($status->path == DIRECTORY_SEPARATOR) {
            throw new Exception('You shall not pass!');
        }

        $exploded = explode(DIRECTORY_SEPARATOR, $status->path);
        $exploded = array_slice($exploded, 0, -2);
        $imploded = implode(DIRECTORY_SEPARATOR, $exploded);

        $status->path = $this->buildPath((empty($imploded)) ? DIRECTORY_SEPARATOR : $imploded);

        return $status;
    }

    /**
     * Checks if directory exists.
     *
     * @param string $directory Relative directory path, in relate to __ROOT_PATH
     *
     * @return bool Exists?
     */
    private function checkDirectory($directory)
    {
        return \System\Filesystem::checkDirectory(__ROOT_PATH . $directory);
    }

    /**
     * Creates valid path for terminal status.
     *
     * @param string $path Path to valid
     *
     * @return string Valid path
     */
    private function buildPath($path)
    {
        $path = DIRECTORY_SEPARATOR . str_replace(__ROOT_PATH, '', realpath(__ROOT_PATH . $path)) . DIRECTORY_SEPARATOR;

        return str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
    }
}
