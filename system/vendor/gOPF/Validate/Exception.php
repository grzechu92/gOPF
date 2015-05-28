<?php

namespace gOPF\Validate;

/**
 * Validate exception class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Exception extends \Exception
{
    /**
     * Variable name.
     *
     * @var string
     */
    public $name;

    /**
     * Incorrect parameter.
     *
     * @var string
     */
    public $error;

    /**
     * @see \Exception::__construct();
     *
     * @param string $error Wrong parameter
     * @param string $name  Variable name
     */
    public function __construct($error, $name)
    {
        $this->message = 'Validate Error: ' . $error . (!empty($name) ? ' in ' . $name : '');

        $this->name = $name;
        $this->error = $error;
    }

    /**
     * Returns variable name which is wrong.
     *
     * @return string Variable name
     */
    public function getVariableName()
    {
        return $this->name;
    }

    /**
     * Returns parameter which is wrong.
     *
     * @return string Parameter
     */
    public function getError()
    {
        return $this->error;
    }
}
