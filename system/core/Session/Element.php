<?php

namespace System\Session;

/**
 * Session element class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Element
{
    /**
     * Element name.
     *
     * @var string
     */
    public $name;

    /**
     * Element value.
     *
     * @var mixed
     */
    public $value;

    /**
     * Element modified time.
     *
     * @var float
     */
    public $modified;

    /**
     * Element expire time.
     *
     * @var int
     */
    public $expires = 0;

    /**
     * Initializes element, saves with in requested data.
     *
     * @param string $name
     * @param mixed  $value
     * @param int    $lifetime
     */
    public function __construct($name, $value, $lifetime = 0)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expires = ($lifetime > 0) ? $lifetime + time() : 0;
        $this->modified = microtime(true);
    }

    /**
     * Extends element lifetime.
     *
     * @param int $lifetime Element lifetime
     */
    public function extend($lifetime)
    {
        $this->expires += $lifetime;
    }
}
