<?php

namespace System\Terminal\Data;

/**
 * Terminal command parameter.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
final class Parameter
{
    /**
     * Parameter name.
     *
     * @var string
     */
    private $name;

    /**
     * Parameter value.
     *
     * @var string
     */
    private $value;

    /**
     * Initialize parameter class.
     *
     * @param string $name  Parameter name
     * @param string $value Parameter value
     */
    public function __construct($name, $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get parameter name.
     *
     * @return string Parameter name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get parameter value.
     *
     * @return string Parameter value
     */
    public function getValue()
    {
        return $this->value;
    }
}
