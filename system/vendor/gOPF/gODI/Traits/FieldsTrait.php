<?php

namespace gOPF\gODI\Traits;

/**
 * FieldsTrait - allows to select field or fields in query.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
trait FieldsTrait
{
    /**
     * Fields array.
     *
     * @var string[]
     */
    private $fields = array();

    /**
     * Allow to select query fields.
     *
     * @param mixed $fields Single field in string, or multiple fields in array
     *
     * @return \gOPF\gODI\Statement
     */
    public function fields($fields)
    {
        if (is_array($fields)) {
            $this->fields = $fields;
        } else {
            $this->fields[] = $fields;
        }

        return $this;
    }
}
