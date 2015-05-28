<?php

namespace System\Database;

/**
 * Database engine interface.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
interface EngineInterface
{
    /**
     * Saves engine config into class.
     *
     * @param array $config Engine config
     */
    public function __construct($config);

    /**
     * Connects to database using selected engine.
     */
    public function connect();

    /**
     * Returns handler to database.
     *
     * @return mixed Database handler
     */
    public function handler();

    /**
     * Execute query no matter what engine is selected.
     *
     * @param string $query  Query to execute
     * @param bool   $result Is query must expect any result
     *
     * @return \stdClass Query result
     */
    public function query($query, $result = false);

    /**
     * Unified transaction interface.
     *
     * @return \System\Database\TransactionInterface
     */
    public function transaction();
}
