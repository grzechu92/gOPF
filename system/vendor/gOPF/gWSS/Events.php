<?php

namespace gOPF\gWSS;

/**
 * gWSS events class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Events
{
    /**
     * Events from client.
     *
     * @var \System\Events
     */
    public $client;

    /**
     * Events for client.
     *
     * @var \System\Events
     */
    public $server;

    /**
     * Initialize events module.
     */
    public function __construct()
    {
        $this->client = new \System\Events();
        $this->server = new \System\Events();
    }
}
