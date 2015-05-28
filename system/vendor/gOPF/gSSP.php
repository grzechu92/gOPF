<?php

namespace gOPF;

use gOPF\gSSP\Slot;
use gOPF\gSSP\Server;

/**
 * gSSP - gSSP Server Status Parser.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class gSSP
{
    /**
     * Server status URL name.
     *
     * @var string
     */
    const SERVER_STATUS = 'server-status';

    /**
     * Server stats page URL address.
     *
     * @var string
     */
    private $url;

    /**
     * Parsed slots array.
     *
     * @var array
     */
    private $slots = array();

    /**
     * Server information and stats.
     *
     * @var \gOPF\gSSP\Server;
     */
    private $server;

    /**
     * Initiates gSSP object.
     *
     * @param string $url Server stats page URL address
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->update();
    }

    /**
     * Updates data about server status.
     */
    public function update()
    {
        $this->parse(file_get_contents($this->url));
    }

    /**
     * Returns available slots.
     *
     * @param bool $working Return only currently working slots
     * @param int  $time    Returns slots which changes their status from selected timestamp
     *
     * @return array Array with slots
     */
    public function getSlots($working = true, $time = 0)
    {
        $slots = array();

        foreach ($this->slots as $slot) {
            if (!$working || $slot->isWorking()) {
                if (!$time || $time <= $slot->time) {
                    $slots[] = $slot;
                }
            }
        }

        return $slots;
    }

    /**
     * Returns available slots grouped by host.
     *
     * @param bool $working Return only currently working slots
     * @param int  $time    Returns slots which changes their status from selected timestamp
     *
     * @return array Array with slots
     */
    public function getSlotsByHost($working = true, $time = 0)
    {
        return $this->groupSlotsBy('host', $working, $time);
    }

    /**
     * Returns all available slots grouped by client IP address.
     *
     * @param bool $working Return only currently working slots
     * @param int  $time    Returns slots which changes their status from selected timestamp
     *
     * @return array Array with slots
     */
    public function getSlotsByClient($working = true, $time = 0)
    {
        return $this->groupSlotsBy('client', $working, $time);
    }

    /**
     * Returns all available slots grouped by request.
     *
     * @param bool $working Return only currently working slots
     * @param int  $time    Returns slots which changes their status from selected timestamp
     *
     * @return array Array with slots
     */
    public function getSlotsByRequest($working = true, $time = 0)
    {
        return $this->groupSlotsBy('request', $working, $time);
    }

    /**
     * Returns information about server.
     *
     * @return \gOPF\gSSP\Server Server information and stats
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Return server stats.
     *
     * @return \gOPF\gSSP\Stats Server slots stats
     */
    public function getSlotStats()
    {
        return new gSSP\Stats($this->getSlots(false, 86400));
    }

    /**
     * Parses data with server status.
     *
     * @param string $data Server status page to parse
     */
    private function parse($data)
    {
        $this->slots = array();
        $this->server = new Server();
        $fields = array('version', '', '', 'time', 'start', '', '', 'uptime', 'load', 'traffic', 'cpu', 'stats', 'requests');
        $field = 0;
        $first = true;

        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->loadHTML($data);

        foreach ($document->getElementsByTagName('dt') as $row) {
            if (!$fields[$field] == '') {
                $this->server->{$fields[$field]} = $row->textContent;
            }

            $field++;
        }

        foreach ($document->getElementsByTagName('tr') as $row) {
            if ($first) {
                $first = false;
                continue;
            }

            $columns = $row->getElementsByTagName('td');

            if ($columns->length > 1) {
                $data = array();

                foreach ($columns as $column) {
                    $data[] = $column->textContent;
                }

                $this->slots[] = new Slot($data);
            }
        }
    }

    /**
     * Groups slots by selected field.
     *
     * @param string $property Return only matching slots by property
     * @param bool   $working  Return only currently working slots
     * @param int    $time     Returns slots which changes their status from selected timestamp
     *
     * @return array Array with grouped slots
     */
    private function groupSlotsBy($property, $working, $time)
    {
        $return = array();

        foreach ($this->slots as $slot) {
            if (!$working || $slot->isWorking()) {
                if (!$time || $time <= $slot->time) {
                    if (!isset($return[$slot->{$property}])) {
                        $return[$slot->{$property}] = array();
                    }

                    $return[$slot->{$property}][] = $slot;
                }
            }
        }

        return $return;
    }
}
