<?php

namespace System;

use System\Events\Event;

/**
 * Framework events manager.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Events
{
    /**
     * Array of events.
     *
     * @var array[]
     */
    private $list = array();

    /**
     * Adds event to list.
     *
     * @param string   $names   Event name, or names separated by space
     * @param \Closure $closure Action to do
     * @param bool     $once    Call event once?
     */
    public function on($names, \Closure $closure, $once = false)
    {
        $names = explode(' ', $names);

        foreach ($names as $name) {
            if (!isset($this->list[$name])) {
                $this->list[$name] = array();
            }

            $this->list[$name][] = new Event($name, $closure, $once);
        }
    }

    /**
     * Call event by name.
     *
     * @param string $name Event name
     * @param mixed  $data Event data (default: null)
     */
    public function call($name, $data = null)
    {
        if (isset($this->list[$name])) {
            /** @var $event \System\Events\Event */
            foreach ($this->list[$name] as $id => $event) {
                $event->closure($data);

                if ($event->once) {
                    unset($this->list[$name][$id]);
                }
            }
        }
    }

    /**
     * Remove event by name.
     *
     * @param string $names Event name
     */
    public function remove($names)
    {
        $names = explode(' ', $names);

        foreach ($names as $name) {
            unset($this->list[$name]);
        }
    }

    /**
     * Return all events.
     *
     * @return \System\Events\Event[]
     */
    public function get()
    {
        $output = array();

        foreach ($this->list as $events) {
            foreach ($events as $event) {
                $output[] = $event;
            }
        }

        return $output;
    }
}
