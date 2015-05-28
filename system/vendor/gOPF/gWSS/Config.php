<?php

namespace gOPF\gWSS;

/**
 * gWSS config class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Config
{
    /**
     * Normal socket type.
     *
     * @var int
     */
    const SOCKET = 0;

    /**
     * WebSocket type.
     *
     * @var int
     */
    const WEBSOCKET = 1;

    /**
     * Server host.
     *
     * @var string
     */
    public $host = '127.0.0.1';

    /**
     * Server port.
     *
     * @var int
     */
    public $port = 8888;

    /**
     * Debug mode.
     *
     * @var bool
     */
    public $debug = false;

    /**
     * Fork refresh time (in microseconds, 1 000 000us = 1s).
     *
     * @var int
     */
    public $refresh = 100000;

    /**
     * Socket server type.
     *
     * @var int
     */
    public $type = self::WEBSOCKET;
}
