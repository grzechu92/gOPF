<?php

namespace gOPF\gSIP;

/**
 * gSIP Position class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Position
{
    /**
     * Coordinate X value.
     *
     * @var int
     */
    public $x = 0;

    /**
     * Coordinate Y value.
     *
     * @var int
     */
    public $y = 0;

    /**
     * Initiates position object.
     *
     * @param int      $x Coordinate X value
     * @param int|bool $y Coordinate Y value
     */
    public function __construct($x, $y = false)
    {
        if ($y === false) {
            $y = $x;
        }

        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Returns array with X and Y coordinate values.
     *
     * @return array Array with values (X and Y)
     */
    public function getPosition()
    {
        return array($this->x, $this->y);
    }
}
