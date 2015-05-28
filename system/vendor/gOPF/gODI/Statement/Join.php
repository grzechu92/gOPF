<?php

namespace gOPF\gODI\Statement;

use gOPF\gODI\Statement;

/**
 * Join statement.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Join
{
    /**
     * @see \gOPF\gODI\Statement::INNER_JOIN
     */
    const INNER_JOIN = 'INNER JOIN';

    /**
     * @see \gOPF\gODI\Statement::LEFT_JOIN
     */
    const LEFT_JOIN = 'LEFT JOIN';

    /**
     * @see \gOPF\gODI\Statement::RIGHT_JOIN
     */
    const RIGHT_JOIN = 'RIGHT JOIN';

    /**
     * @see \gOPF\gODI\Statement::NATURAL_JOIN
     */
    const NATURAL_JOIN = 'NATURAL JOIN';

    /**
     * Parent statement object.
     *
     * @var \gOPF\gODI\Statement
     */
    private $statement;

    /**
     * Table name to join.
     *
     * @var string
     */
    private $table;

    /**
     * Join type.
     *
     * @var string
     */
    private $type;

    /**
     * First database field.
     *
     * @var string
     */
    private $field1;

    /**
     * Second database field.
     *
     * @var string
     */
    private $field2;

    /**
     * Initiates join statement.
     *
     * @param \gOPF\gODI\Statement $statement Parent statement
     * @param string               $table     Table name
     * @param string               $type      Join type
     */
    public function __construct(Statement $statement, $table, $type)
    {
        $this->statement = $statement;
        $this->table = $table;
        $this->type = $type;
    }

    /**
     * Creates join string.
     *
     * @return string
     */
    public function __toString()
    {
        $on = trim(implode(' ', array($this->field1, '=', $this->field2)));

        return trim(implode(' ', array($this->type, $this->table, ($on == '=' ? '' : 'ON ' . $on))));
    }

    /**
     * Connect database on fields.
     *
     * @param string $field1 First database field
     * @param string $field2 Second database field
     *
     * @return \gOPF\gODI\Statement
     */
    public function on($field1, $field2)
    {
        $this->field1 = $field1;
        $this->field2 = $field2;

        return $this->statement;
    }
}
