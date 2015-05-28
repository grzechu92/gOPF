<?php

namespace System\Terminal\Command;

use System\Terminal\Help\Line;
use System\Config;
use System\Config\File;

/**
 * Terminal command: manager (allows to install custom commands).
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Manager extends \System\Terminal\Command
{
    /**
     * Message when command is empty.
     *
     * @var string
     */
    const EMPTY_COMMAND = 'Command name cannot be empty!';

    /**
     * Message when class doesn't exist.
     *
     * @var string
     */
    const WRONG_CLASS = 'Class doesn\'t exist!';

    /**
     * Message when command doesn't exist.
     *
     * @var string
     */
    const COMMAND_NOT_EXISTS = 'Command does\'t exist!';

    /**
     * @see \System\Terminal\CommandInterface::help()
     */
    public function help()
    {
        $lines = array();
        $help = new \System\Terminal\Help('Manage installed commands');

        $lines[] = new Line('manager -install [class]', 'adds new command to command registry');
        $lines[] = new Line('manager -uninstall [command]', 'removes command from command registry');
        $lines[] = new Line('manager -list', 'list all available commands');

        $help->addLines($lines);

        return $help;
    }

    /**
     * @see \System\Terminal\CommandInterface::execute()
     */
    public function execute()
    {
        $config = Config::factory('terminal.ini', Config::APPLICATION, true);
        $output = '';

        if ($this->getParameter('list')) {
            $output = $this->listCommands($config);
        }

        if ($this->getParameter('install')) {
            $class = trim($this->getParameter('install'));

            $output = $this->installCommand($config, $class);
        }

        if ($this->getParameter('uninstall')) {
            $command = trim($this->getParameter('uninstall'));

            $output = $this->uninstallCommand($config, $command);
        }

        if (!empty($output)) {
            $this->buffer($output);
        }
    }

    /**
     * Lists available commands.
     *
     * @param \System\Config\File $config Terminal config
     *
     * @return string Terminal output
     */
    private function listCommands(File $config)
    {
        $commands = array();

        foreach ($config->get('commands') as $command => $class) {
            $commands[] = $command . ' -> ' . $class;
        }

        return implode("\n", $commands);
    }

    /**
     * Adds new command to registry.
     *
     * @param \System\Config\File $config Terminal config
     * @param string              $class  Class with namespace to command
     *
     * @return string Terminal output
     */
    private function installCommand(File $config, $class)
    {
        try {
            $object = new $class();

            if (!($object instanceof \System\Terminal\CommandInterface)) {
                return self::WRONG_CLASS;
            }

            $object->onInstall();
            $config->setArrayValue('commands', $object->getName(), $class);
        } catch (\System\Loader\Exception $e) {
            return self::WRONG_CLASS;
        }
    }

    /**
     * Removes command from registry.
     *
     * @param \System\Config\File $config  Terminal config
     * @param string              $command Command to remove
     *
     * @return string Terminal output
     */
    private function uninstallCommand(File $config, $command)
    {
        if (empty($command)) {
            return self::EMPTY_COMMAND;
        }

        $class = $config->getArrayValue('commands', $command);

        if (empty($class)) {
            return self::COMMAND_NOT_EXISTS;
        }

        /** @var $object \System\Terminal\CommandInterface */
        $object = new $class();
        $object->onUninstall();

        $config->removeFromArray('commands', $command);
    }
}
