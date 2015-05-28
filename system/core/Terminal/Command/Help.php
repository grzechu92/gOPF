<?php

namespace System\Terminal\Command;

use System\Config;
use System\Terminal\Help as TerminalHelp;
use System\Terminal\Help\Line;

/**
 * Terminal command: help (shows available commands with them description).
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Help extends \System\Terminal\Command
{
    /**
     * @see \System\Terminal\CommandInterface::help()
     */
    public function help()
    {
        return new TerminalHelp('Guess what it does...');
    }

    /**
     * @see \System\Terminal\CommandInterface::execute()
     */
    public function execute()
    {
        $help = new TerminalHelp('Available commands and a few of important shortcuts');
        $lines = array();

        $config = Config::factory('terminal.ini', Config::APPLICATION);
        $commands = $config->get('commands');

        foreach ($commands as $name => $class) {
            /** @var \System\Terminal\CommandInterface $class */
            $class = new $class();
            $lines[] = new Line($name, $class->help()->description);
        }

        $lines[] = new Line(Line::SEPARATOR);
        $lines[] = new Line('If you want to upload file, just drag it into terminal');
        $lines[] = new Line('Use TAB key to fill path while writing');
        $lines[] = new Line('Use SHIFT + ` key to reset terminal if something went wrong');
        $lines[] = new Line('Use CTRL + L when mess on the screen is anoying');

        $help->addLines($lines);
        $this->buffer($help->build());
    }
}
