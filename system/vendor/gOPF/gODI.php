<?php

namespace gOPF;

/**
 * gODI - gODI Object Database Interface.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class gODI extends \System\Database\PDO implements \System\Database\EngineInterface
{
    /**
     * Statement object.
     *
     * @var \gOPF\gODI\Handler
     */
    private $statement;

    /**
     * @see \System\Database\EngineInterface::handler()
     */
    public function handler()
    {
        $this->statement = new \gOPF\gODI\Handler($this->handler);

        return $this->statement;
    }

    /**
     * @see \System\Database\EngineInterface::query()
     */
    public function query($query, $result = false)
    {
        $return = $this->statement->raw()->query($query);

        return $result ? $return->fetch(\PDO::FETCH_OBJ) : null;
    }

    /**
     * @see \System\Database\EngineInterface::transaction();
     */
    public function transaction()
    {
        return $this->statement->transaction();
    }
}
