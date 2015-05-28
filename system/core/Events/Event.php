<?php

namespace System\Events;

/**
 * Framework events manager event class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Event
{
    /**
     * Event name.
     *
     * @var string
     */
    public $name;

    /**
     * Call event once?
     *
     * @var bool
     */
    public $once;

    /**
     * Event action.
     *
     * @var \Closure
     */
    private $closure;

    /**
     * Initialize event object.
     *
     * @param string   $name    Event name
     * @param \Closure $closure Event action
     * @param bool     $once    Call event once?
     */
    public function __construct($name, \Closure $closure, $once = false)
    {
        $this->name = $name;
        $this->closure = $closure;
        $this->once = $once;
    }

    /**
     * Execute function closure.
     *
     * @param mixed
     *
     * @return mixed Closure execution result
     */
    public function closure()
    {
        return call_user_func_array($this->closure, func_get_args());
    }
}
