<?php

namespace gOPF\gWSS;

/**
 * Request headers class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Headers
{
    /**
     * Raw headers data.
     *
     * @var string
     */
    private $data;

    /**
     * Initializes Headers object.
     *
     * @param string $data Raw headers data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get value of Sec-WebSocket-Version header.
     *
     * @return int|bool Version or false, when not supported
     */
    public function getWebSocketVersion()
    {
        $found = preg_match("/Sec-WebSocket-Version: (.*)\r\n/", $this->data, $match);

        return $found ? $match[1] : false;
    }

    /**
     * Get value of Sec-WebSocket-Key header.
     *
     * @return string Header value
     */
    public function getWebSocketKey()
    {
        preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $this->data, $match);

        return $match[1];
    }
}
