<?php

namespace System\Terminal;

/**
 * Terminal command interface.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
interface CommandInterface
{
    /**
     * Initialize command.
     *
     * @param \System\Terminal\Data $data
     */
    public function initialize(Data $data);

    /**
     * Executes command logic.
     */
    public function execute();

    /**
     * Return command help.
     *
     * @return \System\Terminal\Help Help content
     */
    public function help();

    /**
     * Event when command is installed.
     */
    public function onInstall();

    /**
     * Event when command is uninstalled.
     */
    public function onUninstall();

    /**
     * Return command name.
     *
     * @return string Command name
     */
    public function getName();

    /**
     * Checks if parameter has been passed with command
     * If is passed, but empty, returns true
     * If is passed and has value, returns value
     * If isn't passed, returns false.
     *
     * @param string $name Parameter name
     *
     * @return string|bool Parameter data
     */
    public function getParameter($name);

    /**
     * Get command value.
     *
     * @return string
     */
    public function getValue();
}
