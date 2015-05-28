<?php

namespace gOPF\gODI;

/**
 * gODI Update statement.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Update extends Statement
{
    use \gOPF\gODI\Traits\ValuesTrait;
    use \gOPF\gODI\Traits\SearchTrait;
    use \gOPF\gODI\Traits\LimitTrait;
    use \gOPF\gODI\Traits\SortTrait;

    /**
     * @see \gOPF\gODI\Statement::build()
     */
    public function build()
    {
        $parts = array(
            'UPDATE ' . $this->table,
            'SET ' . implode($this->values, ', '),
            (!empty($this->search) ? 'WHERE ' . implode(' ', $this->search) : ''),
            (!empty($this->orderBy) ? 'ORDER BY ' . implode(' ', array($this->orderBy, $this->orderType)) : ''),
            (($this->limitable) ? 'LIMIT :_offset, :_limit' : '')
        );

        return trim(implode(' ', $parts));
    }

    /**
     * Execute statement.
     *
     * @return int Affected rows
     */
    public function make()
    {
        return $this->execute(Statement::RETURN_ROWS);
    }
}
