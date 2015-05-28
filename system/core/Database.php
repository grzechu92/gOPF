<?php

namespace System;

/**
 * Database connection module.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Database extends Singleton
{
    /**
     * Connection status.
     *
     * @var bool
     */
    public $connected = false;

    /**
     * Configuration of database module.
     *
     * @var \System\Config
     */
    private $config;

    /**
     * Connection handler.
     *
     * @var mixed
     */
    private $connection;

    /**
     * Database engine.
     *
     * @var \System\Database\EngineInterface
     */
    private $engine;

    /**
     * Constructor of database module.
     */
    public function __construct()
    {
        $this->config = Config::factory('database.ini', Config::APPLICATION);

        if (!$this->config->lazy) {
            $this->connect();
        }
    }

    /**
     * Connects to database if connection not exist.
     */
    public function connect()
    {
        if (!$this->connected && $this->config->status) {
            $this->loadEngine();
        }
    }

    /**
     * Returns database engine connection, if not exists, connects it to database.
     *
     * @return mixed Database engine handler
     */
    public function connection()
    {
        if (!$this->connected) {
            $this->connect();
        }

        return $this->connection;
    }

    /**
     * Returns database engine.
     *
     * @return \System\Database\EngineInterface Database engine
     */
    public function engine()
    {
        if (!$this->connected) {
            $this->connect();
        }

        return $this->engine;
    }

    /**
     * Loads required database engine.
     */
    private function loadEngine()
    {
        try {
            $this->engine = $engine = new $this->config->engine($this->config->connection);

            if ($engine instanceof \System\Database\EngineInterface) {
                $engine->connect();

                $this->connected = true;
                $this->connection = $engine->handler();
            }
        } catch (\Exception $exception) {
            throw new \System\Database\Exception(\System\I18n::translate('DATABASE_ERROR', array($exception->getMessage())));
        }
    }
}
