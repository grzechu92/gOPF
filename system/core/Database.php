<?php
	namespace System;
	
	/**
	 * Database connection module
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Database {
		/**
		 * Connection status
		 * @var bool
		 */
		public $connected = false;
		
		/**
		 * Configuration of database module
		 * @var Config
		 */
		private $config;
		
		/**
		 * Connection handler
		 * @var mixed
		 */
		private $connection;
		
		/**
		 * Constructor of database module
		 */
		public function __construct() {
			$this->config = new Config('database.ini', Config::APPLICATION);
			
			if (!$this->config->lazy) {
				$this->connect();
			}
		}
		
		/**
		 * Connects to database if connection not exist
		 */
		public function connect() {
			if (!$this->connected && $this->config->status) {
				$this->loadEngine();
			}
		}
		
		/**
		 * Returns database engine connection, if not exists, connects it to database
		 * @return mixed Database engine handler
		 */
		public function connection() {
			if (!$this->connected) {
				$this->connect();
			}
			
			return $this->connection;
		}
		
		/**
		 * Loads required database engine
		 */
		private function loadEngine() {			
			$engine = '\\System\\Database\\'.$this->config->engine;
			$engine = new $engine($this->config->{$this->config->engine});
			
			$this->connection = $engine->connect();
			$this->connected = true;
		}
	}