<?php

namespace gOPF\gODI\Traits;

use gOPF\gODI\Statement\Field;

/**
 * ValuesTrait - allows to define field value in query.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
trait ValuesTrait
{
    /**
     * Fields array.
     *
     * @var \gOPF\gODI\Statement\Field[]
     */
    private $values = array();

    /**
     * Allows to define field value in query.
     *
     * @param string $name  Field name
     * @param mixed  $value Field value
     * @param int    $type  Field value type
     *
     * @return \gOPF\gODI\Statement Fluid interface
     */
    public function field($name, $value, $type = Field::INT)
    {
        $field = new Field($name, $value, $type);
        $this->bind($field->bind);

        $this->values[] = $field;

        return $this;
    }
}
